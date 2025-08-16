import Link from "next/link";

export default function ArticleCard({ a }:{ a:any }) {
    return (
        <article className="bg-white border rounded p-4 hover:shadow">
            <h3 className="font-semibold text-lg mb-1">
                <Link href={`/makale/${a.slug}`}>{a.title}</Link>
            </h3>
            <div className="text-sm text-gray-500 mb-2">
                {a.category?.name ?? "-"} • {a.published_at ?? ""}
            </div>
            <p className="text-sm line-clamp-3">{a.summary ?? ""}</p>
        </article>
    );
}
