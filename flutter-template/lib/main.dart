import 'package:flutter/material.dart';
import 'package:onesignal_flutter/onesignal_flutter.dart';
import 'config.dart';
import 'screens/splash_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Load settings from config.json
  await AppConfig.loadConfig();

  // Initialize OneSignal only when push notification is enabled and App ID is set
  if (AppConfig.enablePushNotification && AppConfig.onesignalAppId.isNotEmpty) {
    try {
      OneSignal.Debug.setLogLevel(OSLogLevel.none);
      OneSignal.initialize(AppConfig.onesignalAppId);
      await OneSignal.Notifications.requestPermission(false);
    } catch (e) {
      debugPrint("OneSignal initialization failed: $e");
    }
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
