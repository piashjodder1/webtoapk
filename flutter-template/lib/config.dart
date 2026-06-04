import 'dart:convert';
import 'package:flutter/services.dart';

class AppConfig {
  static String appName = "Website To App";
  static String websiteUrl = "https://laravel.com";
  static String packageName = "com.example.webviewapp";
  static bool enablePullRefresh = true;
  static bool enableOfflinePage = true;

  static Future<void> loadConfig() async {
    try {
      final String response = await rootBundle.loadString('assets/config.json');
      final data = await json.decode(response);
      
      appName = data['app_name'] ?? appName;
      websiteUrl = data['website_url'] ?? websiteUrl;
      packageName = data['package_name'] ?? packageName;
      enablePullRefresh = data['enable_pull_refresh'] ?? enablePullRefresh;
      enableOfflinePage = data['enable_offline_page'] ?? enableOfflinePage;

    } catch (e) {
      print("Error loading config.json: $e");
    }
  }
}
