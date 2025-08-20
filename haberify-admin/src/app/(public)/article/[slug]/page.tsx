import { api } from "@/lib/public-api";
import Comments from "@/components/Comments";
import Image from "next/image";
import type { Metadata } from "next";

type Props = { params:{ slug:string } };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
    const a = await api.article(params.slug, { revalidate: 60 }).catch(()=>null);
    if (!a) return { title: "Haberify" };
    return {
        title: a.title,
        description: a.summary ?? undefined,
        openGraph: { title: a.title, description: a.summary ?? undefined, images: a.cover_url ? [{ url: a.cover_url }] : undefined, type: "article" },
    };
}

export default async function ArticlePage({ params }: Props) {
    const a = await api.article(params.slug, { revalidate: 60 });

    const jsonLd = {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": a.title,
        "datePublished": a.published_at,
        "image": a.cover_url ? [a.cover_url] : undefined,
        "articleSection": a.category?.name,
        "inLanguage": a.language || "tr",
    };

    return (
        <article className="prose max-w-none">
            <h1 className="mb-2">{a.title}</h1>
            <div className="text-sm text-gray-500 mb-4">
                {a.category?.name ?? "-"} • {a.published_at ?? ""}
            </div>

            {a.cover_url && (
                <div className="relative w-full h-80 mb-4">
                    <Image src={a.cover_url} alt={a.title} fill className="object-cover rounded border" priority />
                </div>
            )}

            <div dangerouslySetInnerHTML={{ __html: a.body ?? "" }} />

            <div className="mt-10"><Comments articleId={a.id} /></div>

            <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />
        </article>
    );
}
