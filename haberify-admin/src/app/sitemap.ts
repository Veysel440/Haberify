import { api } from "@/lib/public-api";

export default async function sitemap() {
    const base = process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000";
    // @ts-ignore
    const { data = [] } = await api.articles({ per_page: 100 }, { revalidate: 60, tags:['articles'] });
    return [
        { url: `${base}/`, changeFrequency: 'hourly', priority: 1.0 },
        ...data.map((a:any)=>({ url: `${base}/article/${a.slug}`, changeFrequency: 'hourly', priority: 0.8 })),
    ];
}
