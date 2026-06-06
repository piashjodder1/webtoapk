package com.webtoapp.template

import android.content.Context
import android.graphics.Bitmap
import android.net.ConnectivityManager
import android.net.Network
import android.net.NetworkCapabilities
import android.net.NetworkRequest
import android.os.Bundle
import android.view.View
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.Button
import android.widget.LinearLayout
import android.widget.ProgressBar
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AppCompatActivity
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.caverock.androidsvg.SVG
import com.google.android.material.bottomnavigation.BottomNavigationView
import android.graphics.drawable.PictureDrawable
import android.graphics.drawable.Drawable
import android.widget.ImageView
import coil.load
import android.graphics.Color
import androidx.core.graphics.ColorUtils
import androidx.core.view.WindowInsetsControllerCompat
import android.content.res.ColorStateList

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var swipeRefresh: SwipeRefreshLayout
    private lateinit var progressBar: ProgressBar
    private lateinit var offlineView: LinearLayout
    private lateinit var retryButton: Button
    private lateinit var bottomNavigation: BottomNavigationView
    private lateinit var customHeader: LinearLayout
    private lateinit var headerLogo: ImageView
    private lateinit var headerLoadingProgress: ProgressBar

    private var isOffline = false
    private var isPageLoading = false
    private var networkCallback: ConnectivityManager.NetworkCallback? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // Initialize UI Elements
        webView = findViewById(R.id.webview)
        swipeRefresh = findViewById(R.id.swipe_refresh)
        progressBar = findViewById(R.id.loading_progress)
        offlineView = findViewById(R.id.offline_view)
        retryButton = findViewById(R.id.retry_button)
        bottomNavigation = findViewById(R.id.bottom_navigation)
        customHeader = findViewById(R.id.custom_header)
        headerLogo = findViewById(R.id.header_logo)
        headerLoadingProgress = findViewById(R.id.header_loading_progress)

        setupCustomHeader()
        setupBottomNavigation()
        applyThemeColor()

        // Retry button reloads the website
        retryButton.setOnClickListener {
            if (isNetworkAvailable()) {
                hideOffline()
                webView.loadUrl(AppConfig.websiteUrl)
            }
        }

        // Pull to Refresh configuration
        swipeRefresh.isEnabled = AppConfig.enablePullRefresh
        swipeRefresh.setOnRefreshListener {
            if (!isOffline) {
                webView.reload()
            } else {
                swipeRefresh.isRefreshing = false
            }
        }

        // Initialize WebView
        setupWebView()

        // Handle native back button navigation
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack()
                } else {
                    if (AppConfig.enableExitConfirmation) {
                        android.app.AlertDialog.Builder(this@MainActivity)
                            .setTitle("Exit App")
                            .setMessage("Are you sure you want to exit?")
                            .setPositiveButton("Yes") { _, _ -> finish() }
                            .setNegativeButton("No", null)
                            .show()
                    } else {
                        finish()
                    }
                }
            }
        })

        // Check network status & register callback
        checkInitialConnectivity()
        registerNetworkCallback()
    }

    private fun setupCustomHeader() {
        if (AppConfig.enableCustomHeader) {
            customHeader.visibility = View.VISIBLE
            if (AppConfig.headerLogoUrl.isNotEmpty()) {
                headerLogo.load(AppConfig.headerLogoUrl) {
                    crossfade(true)
                    setHeader("ngrok-skip-browser-warning", "true")
                    error(android.R.drawable.ic_dialog_alert)
                }
            }
        } else {
            customHeader.visibility = View.GONE
        }
    }

    private fun setupBottomNavigation() {
        if (AppConfig.enableBottomNavigation && AppConfig.bottomNavigationItems != null) {
            bottomNavigation.visibility = View.VISIBLE
            val items = AppConfig.bottomNavigationItems!!
            val menu = bottomNavigation.menu
            
            for (i in 0 until items.length()) {
                val itemObj = items.getJSONObject(i)
                val name = itemObj.optString("name", "Item")
                val url = itemObj.optString("url", "")
                val svgString = itemObj.optString("svg", "")
                
                val menuItem = menu.add(0, i, i, name)
                
                if (svgString.isNotEmpty()) {
                    try {
                        val svg = SVG.getFromString(svgString)
                        val drawable = PictureDrawable(svg.renderToPicture())
                        menuItem.icon = drawable
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                }
            }
            
            bottomNavigation.setOnItemSelectedListener { item ->
                val index = item.itemId
                val itemObj = items.getJSONObject(index)
                val url = itemObj.optString("url", "")
                if (url.isNotEmpty()) {
                    webView.loadUrl(url)
                }
                true
            }
        } else {
            bottomNavigation.visibility = View.GONE
        }
    }

    private fun applyThemeColor() {
        try {
            val color = Color.parseColor(AppConfig.themeColor)
            val isLight = ColorUtils.calculateLuminance(color) > 0.5
            
            // Set Status Bar Color
            window.statusBarColor = color
            WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = isLight
            
            // Set Header Background
            if (AppConfig.enableCustomHeader) {
                customHeader.setBackgroundColor(color)
            }
            
            // Set Bottom Navigation Background and Icon/Text Colors
            if (AppConfig.enableBottomNavigation) {
                bottomNavigation.setBackgroundColor(color)
                
                val itemColor = if (isLight) Color.BLACK else Color.WHITE
                val states = arrayOf(
                    intArrayOf(android.R.attr.state_checked),
                    intArrayOf(-android.R.attr.state_checked)
                )
                val colors = intArrayOf(itemColor, ColorUtils.setAlphaComponent(itemColor, 150))
                val colorStateList = ColorStateList(states, colors)
                
                bottomNavigation.itemIconTintList = colorStateList
                bottomNavigation.itemTextColor = colorStateList
            }
        } catch (e: Exception) {
            e.printStackTrace()
        }
    }

    private fun setupWebView() {
        val settings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true
        settings.useWideViewPort = true
        settings.loadWithOverviewMode = true
        settings.supportZoom()

        webView.webChromeClient = object : WebChromeClient() {
            override fun onProgressChanged(view: WebView?, newProgress: Int) {
                super.onProgressChanged(view, newProgress)
                if (AppConfig.enableLoadingProgressBar) {
                    progressBar.progress = newProgress
                    headerLoadingProgress.visibility = View.VISIBLE
                    if (newProgress == 100) {
                        progressBar.visibility = View.GONE
                        headerLoadingProgress.visibility = View.GONE
                        swipeRefresh.isRefreshing = false
                    } else {
                        progressBar.visibility = View.VISIBLE
                    }
                } else {
                    progressBar.visibility = View.GONE
                    headerLoadingProgress.visibility = View.GONE
                    if (newProgress == 100) {
                        swipeRefresh.isRefreshing = false
                    }
                }
            }
        }

        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                val url = request?.url?.toString() ?: return false
                val uri = android.net.Uri.parse(url)
                val scheme = uri.scheme

                if (scheme == "http" || scheme == "https") {
                    if (AppConfig.enableExternalLinksInBrowser) {
                        var safeWebsiteUrl = AppConfig.websiteUrl
                        if (!safeWebsiteUrl.startsWith("http")) {
                            safeWebsiteUrl = "https://$safeWebsiteUrl"
                        }
                        val appHost = android.net.Uri.parse(safeWebsiteUrl).host?.removePrefix("www.")
                        val reqHost = uri.host?.removePrefix("www.")

                        if (appHost != null && reqHost != null) {
                            if (!reqHost.contains(appHost) && !appHost.contains(reqHost)) {
                                try {
                                    val intent = android.content.Intent(android.content.Intent.ACTION_VIEW, uri)
                                    this@MainActivity.startActivity(intent)
                                    return true
                                } catch (e: Exception) {
                                    e.printStackTrace()
                                }
                            }
                        }
                    }
                    return false
                } else if (scheme == "javascript" || scheme == "about" || scheme == "data" || scheme == null) {
                    return false
                } else {
                    try {
                        val intent = android.content.Intent(android.content.Intent.ACTION_VIEW, uri)
                        this@MainActivity.startActivity(intent)
                        return true
                    } catch (e: Exception) {
                        e.printStackTrace()
                    }
                }
                return super.shouldOverrideUrlLoading(view, request)
            }

            override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                super.onPageStarted(view, url, favicon)
                isPageLoading = true
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                super.onPageFinished(view, url)
                isPageLoading = false
                swipeRefresh.isRefreshing = false
            }

            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                super.onReceivedError(view, request, error)
                if (request?.isForMainFrame == true) {
                    val errorCode = error?.errorCode
                    if (errorCode == WebViewClient.ERROR_HOST_LOOKUP || errorCode == WebViewClient.ERROR_CONNECT || errorCode == WebViewClient.ERROR_TIMEOUT) {
                        showOffline()
                    }
                }
            }
        }
    }

    private fun checkInitialConnectivity() {
        if (!isNetworkAvailable()) {
            showOffline()
        } else {
            webView.loadUrl(AppConfig.websiteUrl)
        }
    }

    private fun isNetworkAvailable(): Boolean {
        val connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val network = connectivityManager.activeNetwork ?: return false
        val activeNetwork = connectivityManager.getNetworkCapabilities(network) ?: return false
        return when {
            activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_WIFI) -> true
            activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_CELLULAR) -> true
            activeNetwork.hasTransport(NetworkCapabilities.TRANSPORT_ETHERNET) -> true
            else -> false
        }
    }

    private fun registerNetworkCallback() {
        val connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val builder = NetworkRequest.Builder()
            .addCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)

        networkCallback = object : ConnectivityManager.NetworkCallback() {
            override fun onAvailable(network: Network) {
                super.onAvailable(network)
                runOnUiThread {
                    if (isOffline) {
                        hideOffline()
                        webView.loadUrl(AppConfig.websiteUrl)
                    }
                }
            }

            override fun onLost(network: Network) {
                super.onLost(network)
                runOnUiThread {
                    if (!isNetworkAvailable()) {
                        showOffline()
                    }
                }
            }
        }
        connectivityManager.registerNetworkCallback(builder.build(), networkCallback!!)
    }

    private fun showOffline() {
        isOffline = true
        swipeRefresh.isRefreshing = false
        if (AppConfig.enableOfflinePage) {
            webView.visibility = View.GONE
            swipeRefresh.visibility = View.GONE
            offlineView.visibility = View.VISIBLE
        }
    }

    private fun hideOffline() {
        isOffline = false
        offlineView.visibility = View.GONE
        webView.visibility = View.VISIBLE
        swipeRefresh.visibility = View.VISIBLE
    }

    override fun onDestroy() {
        networkCallback?.let {
            val connectivityManager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
            connectivityManager.unregisterNetworkCallback(it)
        }
        super.onDestroy()
    }
}
