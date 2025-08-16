import { api } from "@/lib/public-api";
import ArticleCard from "@/components/ArticleCard";

export const revalidate = 60;

export default async function PageNum({ params }:{ params:{ num:string }}) {
    const p = Number(params.num) || 1;
    const { data = [] } = await api.articles({ page: p, per_page: 12 }, { revalidate });
    return <div className="grid grid-cols-1 md:grid-cols-3 gap-4">{data.map((a:any)=><ArticleCard key={a.id} a={a} />)}</div>;
}
