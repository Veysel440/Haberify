const BASE = (process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api/v1").replace(/\/+$/,'');
type FetchOpt = { revalidate?: number | false; nextTags?: string[] };

async function jfetch<T>(path: string, opt: FetchOpt = {}): Promise<T> {
    const { revalidate = 60 } = opt;
    const r = await fetch(`${BASE}${path}`, { next: { revalidate } });
    const j = await r.json();
    return (j?.data ?? j) as T;
}

export const api = {
    settings: (key: string, opt?: FetchOpt) => jfetch<{key:string; value:string}>(`/settings/${encodeURIComponent(key)}`, opt),
    menu: (name: string, opt?: FetchOpt) => jfetch<Record<string, any>>(`/menus/${encodeURIComponent(name)}`, opt),
    articles: (params: Record<string, any> = {}, opt?: FetchOpt) => {
        const q = new URLSearchParams(Object.entries(params).map(([k,v])=>[k,String(v)])).toString();
        return jfetch<{data:any[]; meta:any}>(`/articles${q?`?${q}`:''}`, opt);
    },
    article: (slug: string, opt?: FetchOpt) => jfetch<any>(`/articles/${encodeURIComponent(slug)}`, opt),
    category: (slug: string, opt?: FetchOpt) => jfetch<any>(`/categories/${encodeURIComponent(slug)}`, opt),
    tag: (slug: string, opt?: FetchOpt) => jfetch<any>(`/tags/${encodeURIComponent(slug)}`, opt),
    comments: (articleId: number, opt?: FetchOpt) => jfetch<any[]>(`/articles/${articleId}/comments`, opt),
    search: (q: string, page = 1, opt?: FetchOpt) => jfetch<any>(`/search?q=${encodeURIComponent(q)}&page=${page}`, opt),
};
