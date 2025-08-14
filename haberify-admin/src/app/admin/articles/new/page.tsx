"use client";
import { useRouter } from "next/navigation";
import ArticleForm from "@/components/ArticleForm";

export default function NewArticlePage(){
    const router = useRouter();
    return (
        <div>
            <h1 className="text-xl font-semibold mb-4">Yeni Makale</h1>
            <ArticleForm mode="create" onSaved={(a)=> router.push(`/admin/articles/${a.slug}`)} />
        </div>
    );
}
