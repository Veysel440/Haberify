import { useEffect, useState } from "react";

type NewsMetaProps = { id: number };

export default function NewsDetailMeta({ id }: NewsMetaProps) {
    const [meta, setMeta] = useState<any>(null);

    useEffect(() => {
        fetch(`${process.env.NEXT_PUBLIC_API_URL}/news/${id}/meta`)
            .then(res => res.json())
            .then(setMeta);
    }, [id]);

    if (!meta) return null;

    return (
        <>
            <title>{meta.title}</title>
            <meta name="description" content={meta.description} />
            <meta property="og:title" content={meta.title} />
            <meta property="og:description" content={meta.description} />
            <meta property="og:image" content={meta.image} />
            <meta property="og:url" content={meta.url} />
        </>
    );
}
