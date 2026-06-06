package com.webtoapp.template

import android.content.Context
import org.json.JSONObject

object AppConfig {
    var appName: String = "App"
    var websiteUrl: String = "https://example.com"
    var packageName: String = "com.webtoapp.template"
    var enablePullRefresh: Boolean = true
    var enableOfflinePage: Boolean = true
    var enablePushNotification: Boolean = false
    var enableExitConfirmation: Boolean = false
    var enableLoadingProgressBar: Boolean = false
    var enableExternalLinksInBrowser: Boolean = false
    var onesignalAppId: String = ""
    var versionName: String = "1.0.0"
    var versionCode: Int = 1

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
            enableExitConfirmation = json.optBoolean("enable_exit_confirmation", enableExitConfirmation)
            enableLoadingProgressBar = json.optBoolean("enable_loading_progress_bar", enableLoadingProgressBar)
            enableExternalLinksInBrowser = json.optBoolean("enable_external_links_in_browser", enableExternalLinksInBrowser)
            onesignalAppId = json.optString("onesignal_app_id", onesignalAppId)
            versionName = json.optString("version_name", versionName)
            versionCode = json.optInt("version_code", versionCode)
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }
}
