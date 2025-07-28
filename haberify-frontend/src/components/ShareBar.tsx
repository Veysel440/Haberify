"use client";
import { useEffect, useState } from "react";

type ShareBarProps = {
    title: string;
};

export default function ShareBar({ title }: ShareBarProps) {
    const [shareUrl, setShareUrl] = useState("");

    useEffect(() => {
        setShareUrl(window.location.href);
    }, []);

    const handleCopy = () => {
        navigator.clipboard.writeText(shareUrl);
        alert("Bağlantı kopyalandı!");
    };

    return (
        <div className="flex gap-3 items-center mt-2 mb-4">
            <span className="text-gray-700 text-sm font-medium">Paylaş:</span>
            <a
                href={`https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(shareUrl)}`}
                target="_blank"
                rel="noopener noreferrer"
                className="text-blue-400 hover:underline text-sm"
            >
                Twitter
            </a>
            <a
                href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`}
                target="_blank"
                rel="noopener noreferrer"
                className="text-blue-700 hover:underline text-sm"
            >
                Facebook
            </a>
            <button
                onClick={handleCopy}
                className="text-gray-500 hover:underline text-sm"
                type="button"
            >
                Linki Kopyala
            </button>
        </div>
    );
}
