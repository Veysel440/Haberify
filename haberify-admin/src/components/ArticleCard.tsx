import Link from "next/link";
import Image from "next/image";

export default function ArticleCard({ a }:{ a:any }) {
    return (
        <article className="bg-white border rounded p-4 hover:shadow">
            {a.cover_url && (
                <div className="mb-2 relative w-full h-40">
                    <Image src={a.cover_url} alt={a.title} fill sizes="(max-width:768px) 100vw, 33vw" className="object-cover rounded" />
                </div>
            )}
            <h3 className="font-semibold text-lg mb-1"><Link href={`/article/${a.slug}`}>{a.title}</Link></h3>
            <div className="text-sm text-gray-500 mb-2">{a.category?.name ?? "-"} • {a.published_at ?? ""}</div>
            <p className="text-sm line-clamp-3">{a.summary ?? ""}</p>
        </article>
    );
}
