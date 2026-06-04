package com.webtoapp.template

import android.content.Intent
import android.graphics.drawable.Drawable
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.widget.ImageView
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity

class SplashActivity : AppCompatActivity() {

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_splash)

        // Load config from assets/config.json
        AppConfig.loadConfig(this)

        // Set dynamic app name
        val appNameTextView: TextView = findViewById(R.id.splash_app_name)
        appNameTextView.text = AppConfig.appName

        // Try loading custom logo from assets
        val logoImageView: ImageView = findViewById(R.id.splash_logo)
        try {
            assets.open("logo.png").use { inputStream ->
                val drawable = Drawable.createFromStream(inputStream, null)
                logoImageView.setImageDrawable(drawable)
            }
        } catch (e: Exception) {
            // Fallback: Use standard default Android icon if custom icon is not set yet
            logoImageView.setImageResource(android.R.drawable.ic_menu_compass)
        }

        // Navigate to MainActivity after 1.5 seconds
        Handler(Looper.getMainLooper()).postDelayed({
            val intent = Intent(this@SplashActivity, MainActivity::class.java)
            startActivity(intent)
            finish()
        }, 1500)
    }
}
