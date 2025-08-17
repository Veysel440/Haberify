import "./globals.css";
import { Inter } from "next/font/google";

const inter = Inter({ subsets: ["latin"] });

export const metadata = { title: "Haberify" };

export default function RootLayout({ children }: { children: React.ReactNode }) {
    const api = (process.env.NEXT_PUBLIC_API_URL || "").replace(/\/+$/,'');
    return (
        <html lang="tr">
        <head>
            {api && <link rel="preconnect" href={api} crossOrigin="" />}
        </head>
        <body className={inter.className + " bg-gray-50 text-gray-900"}>{children}</body>
        </html>
    );
}
