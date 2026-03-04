import type { NextConfig } from "next";
import { webpack } from "next/dist/compiled/webpack/webpack";

const nextConfig: NextConfig = {
 turbopack: {},   
 transpilePackages: ['@popperjs/core'], 
 devIndicators: false,  //disable bottom page icon
  webpack: (config) => {
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
