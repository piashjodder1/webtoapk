# ProGuard rules for Website To App
# Minification is disabled by default, but these rules are kept for future use.

-keepattributes *Annotation*
-keepattributes SourceFile,LineNumberTable

# Keep WebView classes
-keepclassmembers class * extends android.webkit.WebViewClient {
    public void onPageStarted(android.webkit.WebView, java.lang.String, android.graphics.Bitmap);
    public void onPageFinished(android.webkit.WebView, java.lang.String);
    public void onReceivedError(android.webkit.WebView, android.webkit.WebResourceRequest, android.webkit.WebResourceError);
}

# Keep JavaScript interface methods
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
