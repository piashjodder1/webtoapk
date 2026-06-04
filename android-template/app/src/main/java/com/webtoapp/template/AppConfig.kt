package com.webtoapp.template

import android.content.Context
import org.json.JSONObject

object AppConfig {
    var appName: String = "Website To App"
    var websiteUrl: String = "https://laravel.com"
    var packageName: String = "com.webtoapp.template"
    var enablePullRefresh: Boolean = true
    var enableOfflinePage: Boolean = true
    var enablePushNotification: Boolean = false
    var onesignalAppId: String = ""

    fun loadConfig(context: Context) {
        try {
            val jsonString = context.assets.open("config.json").bufferedReader().use { it.readText() }
            val json = JSONObject(jsonString)
            appName = json.optString("app_name", appName)
            websiteUrl = json.optString("website_url", websiteUrl)
            packageName = json.optString("package_name", packageName)
            enablePullRefresh = json.optBoolean("enable_pull_refresh", enablePullRefresh)
            enableOfflinePage = json.optBoolean("enable_offline_page", enableOfflinePage)
            enablePushNotification = json.optBoolean("enable_push_notification", enablePushNotification)
            onesignalAppId = json.optString("onesignal_app_id", onesignalAppId)
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }
}
