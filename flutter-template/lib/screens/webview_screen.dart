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
  // connectivity_plus v5.0.2 returns ConnectivityResult, not List
  late StreamSubscription<ConnectivityResult> _connectivitySubscription;

  @override
  void initState() {
    super.initState();
    _initWebView();
    _checkInitialConnectivity();
    _setupConnectivityListener();
  }

  @override
  void dispose() {
    _connectivitySubscription.cancel();
    super.dispose();
  }

  // Check initial connection status
  void _checkInitialConnectivity() async {
    try {
      final ConnectivityResult result = await Connectivity().checkConnectivity();
      if (result == ConnectivityResult.none) {
        if (mounted) {
          setState(() => _isOffline = true);
          _loadOfflinePage();
        }
      }
    } catch (e) {
      debugPrint('Connectivity check failed: $e');
    }
  }

  // Setup dynamic connectivity listener
  void _setupConnectivityListener() {
    _connectivitySubscription =
        Connectivity().onConnectivityChanged.listen((ConnectivityResult result) {
      final bool offline = (result == ConnectivityResult.none);
      if (offline) {
        if (mounted) {
          setState(() => _isOffline = true);
          _loadOfflinePage();
        }
      } else {
        if (_isOffline && mounted) {
          setState(() => _isOffline = false);
          _controller.loadRequest(Uri.parse(AppConfig.websiteUrl));
        }
      }
    });
  }

  // Initialize WebViewController
  void _initWebView() {
    _controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setBackgroundColor(Colors.white)
      ..setNavigationDelegate(
        NavigationDelegate(
          onProgress: (int progress) {
            if (mounted) {
              setState(() {
                _loadingProgress = progress / 100;
              });
            }
          },
          onPageStarted: (String url) {
            if (mounted) {
              setState(() => _isLoading = true);
            }
          },
          onPageFinished: (String url) {
            if (mounted) {
              setState(() => _isLoading = false);
            }
          },
          onWebResourceError: (WebResourceError error) {
            // Only show offline page for main frame network errors
            if (error.isForMainFrame == true &&
                (error.errorCode == -2 ||
                    error.description
                        .contains('net::ERR_INTERNET_DISCONNECTED') ||
                    error.description.contains('net::ERR_NAME_NOT_RESOLVED') ||
                    error.description.contains('net::ERR_CONNECTION_REFUSED'))) {
              if (mounted) {
                setState(() => _isOffline = true);
                _loadOfflinePage();
              }
            }
          },
        ),
      );

    // Load the website URL after controller is ready
    _controller.loadRequest(Uri.parse(AppConfig.websiteUrl));
  }

  // Load offline.html locally
  void _loadOfflinePage() {
    if (AppConfig.enableOfflinePage) {
      try {
        _controller.loadFlutterAsset('assets/offline.html');
      } catch (e) {
        debugPrint('Failed to load offline page: $e');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (bool didPop, dynamic result) async {
        if (didPop) return;
        if (await _controller.canGoBack()) {
          _controller.goBack();
        } else {
          if (context.mounted) {
            Navigator.of(context).pop();
          }
        }
      },
      child: Scaffold(
        backgroundColor: Colors.white,
        body: SafeArea(
          child: Column(
            children: [
              // Top loading progress bar
              if (_isLoading)
                LinearProgressIndicator(
                  value: _loadingProgress > 0 ? _loadingProgress : null,
                  backgroundColor: Colors.transparent,
                  valueColor:
                      const AlwaysStoppedAnimation<Color>(Color(0xFF4F46E5)),
                  minHeight: 3.0,
                ),

              // WebView Area
              Expanded(
                child: AppConfig.enablePullRefresh && !_isOffline
                    ? RefreshIndicator(
                        onRefresh: () async {
                          await _controller.reload();
                        },
                        child: WebViewWidget(controller: _controller),
                      )
                    : WebViewWidget(controller: _controller),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
