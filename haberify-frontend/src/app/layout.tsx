import { ReactNode } from 'react';
import Navbar from '@/components/Navbar';
import { AuthProvider } from '@/contexts/AuthContext';
import './globals.css';

export default function RootLayout({ children }: { children: ReactNode }) {
    return (
        <html lang="tr">
        <body className="bg-gray-50 min-h-screen">
        <AuthProvider>
            <Navbar />
            <main className="max-w-4xl mx-auto mt-6">{children}</main>
        </AuthProvider>
        </body>
        </html>
    );
}
