"use client";
import React, { useEffect, useState } from "react";
import { useForm, type SubmitHandler, type Resolver } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { API, pickData } from "@/lib/api";

const schema = z.object({
    title: z.string().min(3, "en az 3 karakter"),
    slug: z.string().optional(),
    category_id: z.coerce.number(),
    summary: z.string().optional(),
    body: z.string().optional(),
    language: z.enum(["tr", "en"]).default("tr"),
    status: z.enum(["draft", "published", "scheduled"]).default("draft"),
    tag_ids: z.array(z.number()).default([]), // undefined yerine []
});
type FormData = z.infer<typeof schema>;

type Props = { mode: "create" | "edit"; initial?: any; onSaved?: (a: any) => void };

export default function ArticleForm({ mode, initial, onSaved }: Props) {
    const { register, handleSubmit, setValue, watch, formState:{ errors, isSubmitting } } =
        useForm<FormData>({
            resolver: zodResolver(schema) as unknown as Resolver<FormData>,
            defaultValues: {
                title: initial?.title ?? "",
                slug: initial?.slug ?? "",
                category_id: initial?.category_id ?? undefined,
                summary: initial?.summary ?? "",
                body: initial?.body ?? "",
                language: (initial?.language as "tr" | "en") ?? "tr",
                status: (initial?.status as "draft" | "published" | "scheduled") ?? "draft",
                tag_ids: initial?.tags?.map((t:any)=>Number(t.id)) ?? [],
            } satisfies Partial<FormData>,
        });

    const [cats, setCats] = useState<any[]>([]);
    const [tags, setTags] = useState<any[]>([]);

    useEffect(() => {
        let alive = true;
        (async () => {
            try {
                const c = await API.get("/categories");
                const t = await API.get("/tags");
                if (!alive) return;
                setCats(pickData<any[]>(c) ?? []);
                setTags(pickData<any[]>(t) ?? []);
            } catch {}
        })();
        return () => { alive = false; };
    }, []);

    const onSubmit: SubmitHandler<FormData> = async (v) => {
        const payload = { ...v, tag_ids: v.tag_ids ?? [] };
        const res = mode === "create"
            ? await API.post("/articles", payload)
            : await API.put(`/articles/${initial?.id}`, payload);
        onSaved?.(pickData(res));
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4 max-w-3xl">
            <div>
                <label className="block text-sm">Başlık</label>
                <input className="w-full border p-2 rounded" {...register("title")} />
                {errors.title && <p className="text-red-600 text-sm">{errors.title.message}</p>}
            </div>

            <div className="grid grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm">Slug</label>
                    <input className="w-full border p-2 rounded" {...register("slug")} />
                </div>
                <div>
                    <label className="block text-sm">Dil</label>
                    <select className="w-full border p-2 rounded" {...register("language")}>
                        <option value="tr">tr</option>
                        <option value="en">en</option>
                    </select>
                </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
                <div>
                    <label className="block text-sm">Kategori</label>
                    <select className="w-full border p-2 rounded" {...register("category_id")}>
                        <option value="">Seç</option>
                        {cats.map((c) => (
                            <option key={c.id} value={c.id}>{c.name}</option>
                        ))}
                    </select>
                    {errors.category_id && <p className="text-red-600 text-sm">kategori zorunlu</p>}
                </div>
                <div>
                    <label className="block text-sm">Durum</label>
                    <select className="w-full border p-2 rounded" {...register("status")}>
                        <option value="draft">Taslak</option>
                        <option value="published">Yayında</option>
                        <option value="scheduled">Planlı</option>
                    </select>
                </div>
            </div>

            <div>
                <label className="block text-sm">Özet</label>
                <textarea className="w-full border p-2 rounded" rows={3} {...register("summary")} />
            </div>

            <div>
                <label className="block text-sm">İçerik</label>
                <textarea className="w-full border p-2 rounded" rows={12} {...register("body")} />
            </div>

            <div>
                <label className="block text-sm">Etiketler</label>
                <div className="grid grid-cols-4 gap-2">
                    {tags.map((t) => (
                        <label key={t.id} className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                value={t.id}
                                checked={(watch("tag_ids") ?? []).includes(t.id)}
                                onChange={(e) => {
                                    const cur = new Set(watch("tag_ids") ?? []);
                                    const id = Number(e.target.value);
                                    e.target.checked ? cur.add(id) : cur.delete(id);
                                    setValue("tag_ids", Array.from(cur), { shouldValidate: true });
                                }}
                            />
                            {t.name}
                        </label>
                    ))}
                </div>
            </div>

            <button disabled={isSubmitting} className="px-4 py-2 bg-black text-white rounded">
                {mode === "create" ? "Oluştur" : "Güncelle"}
            </button>
        </form>
    );
}
