'use client';

import Link from 'next/link';

export default function Navbar() {
    return (
        <nav className="bg-white shadow px-4 py-3 flex justify-between items-center">
            <div className="text-xl font-bold text-blue-600">
                <Link href="/">Haberify</Link>
            </div>
            <div className="flex gap-4">
                <Link href="/news" className="hover:text-blue-500">Haberler</Link>
                <Link href="/login" className="hover:text-blue-500">Giriş Yap</Link>
                <Link href="/register" className="hover:text-blue-500">Kayıt Ol</Link>
                <Link href="/admin" className="hover:text-blue-500">Admin</Link>
            </div>
        </nav>
    );
}
