import { api } from "@/lib/public-api";
import ArticleCard from "@/components/ArticleCard";

export const revalidate = 120;

export default async function TagPage({ params }:{ params:{ slug:string }}) {
    const tag = await api.tag(params.slug, { revalidate });
    const { data = [] } = await api.articles({ tag: params.slug, per_page: 12 }, { revalidate });
    return (
        <div className="space-y-4">
            <h1 className="text-xl font-semibold">Tag: {tag?.name ?? params.slug}</h1>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {data.map((a:any)=><ArticleCard key={a.id} a={a} />)}
            </div>
        </div>
    );
}
