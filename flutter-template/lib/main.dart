import 'package:flutter/material.dart';
import 'package:onesignal_flutter/onesignal_flutter.dart';
import 'config.dart';
import 'screens/splash_screen.dart';

bool _isValidOneSignalId(String id) {
  final cleanId = id.trim();
  if (cleanId.isEmpty ||
      cleanId.toLowerCase() == 'null' ||
      cleanId.toLowerCase() == 'undefined') {
    return false;
  }
  // OneSignal App ID must be a valid 36-character UUID
  final regExp = RegExp(
    r'^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$',
  );
  return regExp.hasMatch(cleanId);
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Load settings from config.json
  await AppConfig.loadConfig();

  // Initialize OneSignal only when enabled and a valid App ID is present
  if (AppConfig.enablePushNotification &&
      _isValidOneSignalId(AppConfig.onesignalAppId)) {
    try {
      OneSignal.Debug.setLogLevel(OSLogLevel.none);
      OneSignal.initialize(AppConfig.onesignalAppId.trim());
      await OneSignal.Notifications.requestPermission(false);
    } catch (e) {
      debugPrint("OneSignal initialization failed: $e");
    }
  } else {
    debugPrint("OneSignal push notifications disabled or invalid App ID.");
  }

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: AppConfig.appName,
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFF4F46E5),
          primary: const Color(0xFF4F46E5),
        ),
      ),
      home: const CustomSplashScreen(),
    );
  }
}
