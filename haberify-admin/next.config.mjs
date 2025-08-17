import type { NextConfig } from "next";

/** @type {import('next').NextConfig} */
const nextConfig = {
    images: {
        remotePatterns: [
            { protocol: 'http', hostname: 'localhost', port: '8000', pathname: '/storage/**' },
            { protocol: 'https', hostname: '**', pathname: '/**' },
        ],
    },
    async headers() {
        return [
            {
                source: '/(.*)\\.js',
                headers: [{ key: 'Cache-Control', value: 'public, max-age=31536000, immutable' }],
            },
            {
                source: '/_next/static/(.*)',
                headers: [{ key: 'Cache-Control', value: 'public, max-age=31536000, immutable' }],
            },
            {
                source: '/fonts/(.*)',
                headers: [{ key: 'Cache-Control', value: 'public, max-age=31536000, immutable' }],
            },
        ];
    },
};
export default nextConfig;
