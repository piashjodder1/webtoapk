import 'dart:async';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/material.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:webview_flutter/webview_flutter.dart';
import '../config.dart';

class WebViewScreen extends StatefulWidget {
  const WebViewScreen({super.key});

  @override
  State<WebViewScreen> createState() => _WebViewScreenState();
}

class _WebViewScreenState extends State<WebViewScreen> {
  late final WebViewController _controller;
  bool _isLoading = true;
  double _loadingProgress = 0;
  bool _isOffline = false;
  late StreamSubscription<ConnectivityResult> _connectivitySubscription;
  
  // AdMob components
  BannerAd? _bannerAd;
  bool _isBannerAdLoaded = false;
  InterstitialAd? _interstitialAd;

  @override
  void initState() {
    super.initState();
    _checkInitialConnectivity();
    _setupConnectivityListener();
    _initWebView();
    _initAds();
  }

  @override
  void dispose() {
    _connectivitySubscription.cancel();
    _bannerAd?.dispose();
    _interstitialAd?.dispose();
    super.dispose();
  }

  // Check initial connection status
  void _checkInitialConnectivity() async {
    final connectivityResult = await (Connectivity().checkConnectivity());
    if (connectivityResult == ConnectivityResult.none) {
      setState(() {
        _isOffline = true;
      });
      _loadOfflinePage();
    }
  }

  // Setup dynamic connectivity listener
  void _setupConnectivityListener() {
    _connectivitySubscription = Connectivity().onConnectivityChanged.listen((ConnectivityResult result) {
      if (result == ConnectivityResult.none) {
        setState(() {
          _isOffline = true;
        });
        _loadOfflinePage();
      } else {
        if (_isOffline) {
          setState(() {
            _isOffline = false;
          });
          _controller.loadRequest(Uri.parse(AppConfig.websiteUrl));
        }
      }
    });
  }

  // Initialize WebViewController
  void _initWebView() {
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            setState(() {
              _loadingProgress = progress / 100;
            });
          },
          onPageStarted: (String url) {
            setState(() {
              _isLoading = true;
            });
          },
          onPageFinished: (String url) {
            setState(() {
              _isLoading = false;
            });
            // Show interstitial ad on page load completion occasionally if enabled
            if (AppConfig.enableAdmob) {
              _showInterstitialAd();
            }
          },
          onWebResourceError: (WebResourceError error) {
            // Check if loading failed due to no network
            if (error.errorCode == -2 || error.description.contains('net::ERR_INTERNET_DISCONNECTED')) {
              setState(() {
                _isOffline = true;
              });
              _loadOfflinePage();
            }
          },
        ),
      );

    if (!_isOffline) {
      _controller.loadRequest(Uri.parse(AppConfig.websiteUrl));
    }
  }

  // Load offline.html locally
  void _loadOfflinePage() {
    if (AppConfig.enableOfflinePage) {
      _controller.loadFlutterAsset('assets/offline.html');
    }
  }

  // Load AdMob Ads
  void _initAds() {
    if (AppConfig.enableAdmob) {
      _loadBannerAd();
      _loadInterstitialAd();
    }
  }

  void _loadBannerAd() {
    final bannerUnitId = AppConfig.admobBannerUnitId.isNotEmpty 
        ? AppConfig.admobBannerUnitId 
        : 'ca-app-pub-3940256099942544/6300978111'; // Google Default Testing ID

    _bannerAd = BannerAd(
      adUnitId: bannerUnitId,
      request: const AdRequest(),
      size: AdSize.banner,
      listener: BannerAdListener(
        onAdLoaded: (ad) {
          setState(() {
            _isBannerAdLoaded = true;
          });
        },
        onAdFailedToLoad: (ad, err) {
          print('BannerAd failed to load: $err');
          ad.dispose();
        },
      ),
    )..load();
  }

  void _loadInterstitialAd() {
    final interstitialUnitId = AppConfig.admobInterstitialUnitId.isNotEmpty
        ? AppConfig.admobInterstitialUnitId
        : 'ca-app-pub-3940256099942544/1033173712'; // Google Default Testing ID

    InterstitialAd.load(
      adUnitId: interstitialUnitId,
      request: const AdRequest(),
      adLoadCallback: InterstitialAdLoadCallback(
        onAdLoaded: (ad) {
          _interstitialAd = ad;
        },
        onAdFailedToLoad: (LoadAdError error) {
          print('InterstitialAd failed to load: $error');
        },
      ),
    );
  }

  void _showInterstitialAd() {
    if (_interstitialAd != null) {
      _interstitialAd!.show();
      _interstitialAd = null; // Reset
      _loadInterstitialAd(); // Preload next
    }
  }

  @override
  Widget build(BuildContext context) {
    return WillPopScope(
      onWillPop: () async {
        if (await _controller.canGoBack()) {
          _controller.goBack();
          return false;
        }
        return true;
      },
      child: Scaffold(
        appBar: const PreferredSize(
          preferredSize: Size.zero, // Hidden Status Bar padding container
          child: SizedBox(),
        ),
        body: Column(
          children: [
            // Top loading progress bar
            if (_isLoading)
              LinearProgressIndicator(
                value: _loadingProgress,
                backgroundColor: Colors.transparent,
                valueColor: const AlwaysStoppedAnimation<Color>(Color(0xFF4F46E5)),
                minHeight: 3.0,
              ),
            
            // WebView Area
            Expanded(
              child: AppConfig.enablePullRefresh && !_isOffline
                  ? RefreshIndicator(
                      onRefresh: () async {
                        _controller.reload();
                      },
                      child: WebViewWidget(controller: _controller),
                    )
                  : WebViewWidget(controller: _controller),
            ),
            
            // Banner Ad Container
            if (AppConfig.enableAdmob && _isBannerAdLoaded && _bannerAd != null)
              Container(
                alignment: Alignment.center,
                width: _bannerAd!.size.width.toDouble(),
                height: _bannerAd!.size.height.toDouble(),
                child: AdWidget(ad: _bannerAd!),
              ),
          ],
        ),
      ),
    );
  }
}
