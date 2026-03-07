import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  serverExternalPackages: ["puppeteer-core", "@sparticuz/chromium"],
  experimental: {
  },
 turbopack: {
    resolveAlias: {
      fs: { browser: './empty.js' },
      path: { browser: './empty.js' },
      stream: { browser: './empty.js' },
    },
  },  
  transpilePackages: ['@popperjs/core'],
  devIndicators: false,  
  webpack: (config, { isServer, webpack }) => {
    // 1.'isServer' logic
    config.resolve.alias.canvas = false;
    if (!isServer) {
      config.resolve.fallback = {
        ...config.resolve.fallback,
        fs: false,
        path: false,
        os: false,
      };
    }

    // 2. Safely push to plugins using the provided webpack instance
    config.plugins = config.plugins || [];
    config.plugins.push(
      new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        Popper: ['@popperjs/core', 'createPopper'],
      })
    );

    return config;
  },
  output: 'export',
  images: {
    unoptimized: true,
  },
  trailingSlash: true,
};

export default nextConfig;
