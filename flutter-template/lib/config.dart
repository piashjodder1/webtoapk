import 'dart:convert';
import 'package:flutter/services.dart';

class AppConfig {
  static String appName = "Website To App";
  static String websiteUrl = "https://laravel.com";
  static String packageName = "com.example.webviewapp";
  static bool enablePullRefresh = true;
  static bool enableOfflinePage = true;
  static bool enablePushNotification = false;
  static String onesignalAppId = "";
  static bool enableAdmob = false;
  static String admobAppId = "";
  static String admobBannerUnitId = "";
  static String admobInterstitialUnitId = "";

  static Future<void> loadConfig() async {
    try {
      final String response = await rootBundle.loadString('assets/config.json');
      final data = await json.decode(response);
      
      appName = data['app_name'] ?? appName;
      websiteUrl = data['website_url'] ?? websiteUrl;
      packageName = data['package_name'] ?? packageName;
      enablePullRefresh = data['enable_pull_refresh'] ?? enablePullRefresh;
      enableOfflinePage = data['enable_offline_page'] ?? enableOfflinePage;
      enablePushNotification = data['enable_push_notification'] ?? enablePushNotification;
      onesignalAppId = data['onesignal_app_id'] ?? onesignalAppId;
      enableAdmob = data['enable_admob'] ?? enableAdmob;
      admobAppId = data['admob_app_id'] ?? admobAppId;
      admobBannerUnitId = data['admob_banner_unit_id'] ?? admobBannerUnitId;
      admobInterstitialUnitId = data['admob_interstitial_unit_id'] ?? admobInterstitialUnitId;
    } catch (e) {
      print("Error loading config.json: $e");
    }
  }
}
