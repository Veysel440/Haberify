'use client';

import { useAuth } from "@/contexts/AuthContext";
import { toggleFavorite } from "@/services/newsApi";
import { useState } from "react";

type NewsCardProps = {
    id: number;
    title: string;
    excerpt: string;
    image?: string | null;
    category?: { id: number; name: string };
    tags?: { id: number; name: string }[];
    is_favorite?: boolean;    
    favorite_count?: number;
};

export default function NewsCard({
                                     id,
                                     title,
                                     excerpt,
                                     image,
                                     category,
                                     tags,
                                     is_favorite = false,
                                     favorite_count = 0,
                                 }: NewsCardProps) {
    const { user } = useAuth();
    const [isFavorite, setIsFavorite] = useState(is_favorite);
    const [favoriteCount, setFavoriteCount] = useState(favorite_count);
    const [loading, setLoading] = useState(false);

    const handleToggleFavorite = async () => {
        if (!user) return alert("Favoriye eklemek için giriş yapmalısın.");
        setLoading(true);
        try {
            await toggleFavorite(id, isFavorite);
            setIsFavorite(!isFavorite);
            setFavoriteCount((c) => isFavorite ? c - 1 : c + 1);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="bg-white rounded-xl shadow p-4 flex gap-4 mb-4">
            {image && (
                <img src={image} alt={title} className="w-32 h-24 object-cover rounded-lg" />
            )}
            <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                    {/* Kategori */}
                    {category && (
                        <span className="text-xs bg-gray-100 text-blue-600 px-2 py-1 rounded">
              {category.name}
            </span>
                    )}
                    {/* Etiketler */}
                    {tags && tags.map((tag) => (
                        <span key={tag.id} className="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
              #{tag.name}
            </span>
                    ))}
                </div>
                <div className="flex items-center gap-2">
                    <h2 className="font-semibold text-lg mb-2 flex-1">{title}</h2>
                    <button
                        onClick={handleToggleFavorite}
                        disabled={loading}
                        className={`text-xl ${isFavorite ? "text-red-600" : "text-gray-400"} transition`}
                        title={user ? (isFavorite ? "Favorilerden çıkar" : "Favorilere ekle") : "Giriş yapmalısın"}
                    >
                        ♥
                    </button>
                    <span className="text-xs text-gray-600">{favoriteCount}</span>
                </div>
                <p className="text-gray-700">{excerpt}</p>
            </div>
        </div>
    );
}
