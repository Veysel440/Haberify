import type { ReactNode } from 'react';
import Navbar from '@/components/Navbar';
import './globals.css';

export default function RootLayout({ children }: { children: ReactNode }) {
    return (
        <html lang="tr">
        <body className="bg-gray-50 min-h-screen">
        <Navbar />
        <main className="max-w-4xl mx-auto mt-6">{children}</main>
        </body>
        </html>
    );
}
