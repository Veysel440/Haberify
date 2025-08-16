import PublicHeader from "./PublicHeader";

export default async function PublicShell({ children }:{children: React.ReactNode}) {
    return (
        <div className="min-h-screen bg-gray-50 text-gray-900">
            <PublicHeader />
            <main className="max-w-5xl mx-auto px-4 py-6">{children}</main>
            <footer className="border-t mt-10 py-6 text-center text-sm text-gray-500">© {new Date().getFullYear()} Haberify</footer>
        </div>
    );
}
