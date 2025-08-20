import { api } from "@/lib/public-api";
import ArticleCard from "@/components/ArticleCard";
import Link from "next/link";

export const revalidate = 60;

export default async function HomePage() {
    const { data = [], meta = {} } = await api.articles({ page: 1, per_page: 12 }, { revalidate });
    return (
        <div className="space-y-6">
            <h1 className="text-2xl font-semibold">Latest News</h1>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {data.map((a:any)=><ArticleCard key={a.id} a={a} />)}
            </div>
            <div className="text-center">
                {meta?.last_page > 1 && <Link prefetch href="/page/2" className="px-3 py-2 border rounded">More</Link>}
            </div>
        </div>
    );
}
