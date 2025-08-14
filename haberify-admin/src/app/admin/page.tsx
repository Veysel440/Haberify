"use client";
import RequireAuth from "@/components/RequireAuth";
import Link from "next/link";

export default function AdminHome(){
    return (
        <RequireAuth>
            <div className="space-y-4">
                <h1 className="text-xl font-semibold">Admin</h1>
                <div className="grid grid-cols-2 gap-4">
                    <Card title="Makaleler" href="/admin/articles" />
                    <Card title="Yorumlar" href="/admin/comments" />
                    <Card title="Analitik" href="/admin/analytics" />
                    <Card title="Çöp Kutusu" href="/admin/trash" />
                </div>
            </div>
        </RequireAuth>
    );
}
function Card({ title, href}:{title:string; href:string}) {
    return <Link href={href} className="block p-6 border rounded bg-white hover:shadow"><div className="font-medium">{title}</div><div className="text-sm text-gray-500">Yönet</div></Link>;
}
