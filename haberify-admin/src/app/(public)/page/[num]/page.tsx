import { api } from "@/lib/public-api";
import ArticleCard from "@/components/ArticleCard";
import Pagination from "@/components/Pagination";

export const revalidate = 60;

export default async function PageNum({ params }:{ params:{ num:string }}) {
    const current = Number(params.num) || 1;
    const { data = [], meta = {} } = await api.articles({ page: current, per_page: 12 }, { revalidate });
    return (
        <div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                {data.map((a:any)=><ArticleCard key={a.id} a={a} />)}
            </div>
            <Pagination basePath="/page" current={current} last={meta?.last_page ?? 1} />
        </div>
    );
}
