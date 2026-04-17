"use client";
import { useEffect } from "react";
import { trackArticleView } from "@/lib/gtm";

type Props = {
    id: number | string;
    slug?: string;
    title?: string;
    category?: string | null;
    language?: string;
};

export default function ArticleViewTracker(props: Props) {
    useEffect(() => {
        trackArticleView(props);
    }, [props]);
    return null;
}
