import 'dart:async';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/material.dart';
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

  @override
  void initState() {
    super.initState();
    _checkInitialConnectivity();
    _setupConnectivityListener();
    _initWebView();
  }

  @override
  void dispose() {
    _connectivitySubscription.cancel();
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
          ],
        ),
      ),
    );
  }
}
