import Link from "next/link";

export default function Pagination({ basePath, current, last }:{ basePath:string; current:number; last:number; }) {
    if (!last || last <= 1) return null;
    const prev = current > 1 ? current - 1 : 1;
    const next = current < last ? current + 1 : last;

    const pages = Array.from({ length: Math.min(7, last) }, (_,i) => {
        // Basit: ilk 7 sayfa. İleri seviye için ... (ellipsis) eklenebilir.
        return i + 1;
    });

    return (
        <nav className="flex items-center gap-2 mt-6">
            <Link prefetch href={`${basePath}/${prev}`} className="px-3 py-1 border rounded">Prev</Link>
            {pages.map(p=>(
                <Link prefetch key={p} href={`${basePath}/${p}`} className={`px-3 py-1 border rounded ${p===current?'bg-black text-white':''}`}>
                    {p}
                </Link>
            ))}
            <Link prefetch href={`${basePath}/${next}`} className="px-3 py-1 border rounded">Next</Link>
        </nav>
    );
}
