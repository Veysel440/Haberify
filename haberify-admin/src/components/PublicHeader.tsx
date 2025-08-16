import Link from "next/link";
import { api } from "@/lib/public-api";

export default async function PublicHeader() {
    const site = await api.settings("site.name").catch(()=>({ value: "Haberify" }));
    const menu = await api.menu("main").catch(()=>({ items: [] as {title:string;url:string}[] }));
    const items: { title:string; url:string }[] = (menu as any).items ?? [];
    return (
        <header className="border-b bg-white">
            <div className="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
                <Link href="/" className="font-bold text-lg">{site?.value ?? "Haberify"}</Link>
                <nav className="flex gap-4 text-sm">
                    {items.map((it,i)=> <Link key={i} href={it.url || "#"} className="hover:underline">{it.title}</Link>)}
                </nav>
            </div>
        </header>
    );
}
