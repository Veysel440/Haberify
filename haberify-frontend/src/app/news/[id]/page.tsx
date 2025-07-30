'use client';

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { fetchNewsDetail } from "@/services/newsApi";
import {
    fetchComments,
    addComment,
    updateComment,
    deleteComment,
    reportComment
} from "@/services/commentApi";
import { useAuth } from "@/contexts/AuthContext";
import ImageGallery from "react-image-gallery";
import "react-image-gallery/styles/css/image-gallery.css";
import VideoPlayer from "@/components/VideoPlayer";
import CommentTree, { CommentType } from "@/components/CommentTree";

type GalleryImage = { id: number; image: string };
type Tag = { id: number; name: string };
type Category = { id: number; name: string };
type News = {
    id: number;
    title: string;
    content: string;
    image?: string | null;
    gallery?: GalleryImage[];
    video?: string | null;
    category?: Category;
    tags?: Tag[];
};

export default function NewsDetailPage() {
    const { id } = useParams<{ id: string }>();
    const [news, setNews] = useState<News | null>(null);
    const [comments, setComments] = useState<CommentType[]>([]);
    const [comment, setComment] = useState("");
    const [error, setError] = useState("");
    const { user } = useAuth();

    // Yorumları getir
    const getComments = () => {
        fetchComments(Number(id)).then(setComments);
    };

    useEffect(() => {
        fetchNewsDetail(Number(id)).then(setNews);
        getComments();
    }, [id]);

    // Yeni yorum ekle
    const handleAddComment = async (e: React.FormEvent) => {
        e.preventDefault();
        setError("");
        if (!comment.trim()) return;
        try {
            await addComment(Number(id), comment);
            setComment("");
            getComments();
        } catch {
            setError("Yorum eklenemedi. Giriş yaptığınızdan emin olun.");
        }
    };

    if (!news) return <div>Yükleniyor...</div>;

    return (
        <div className="bg-white p-6 rounded-xl shadow">
            {/* Kategori ve etiketler başlığın üstünde */}
            <div className="mb-2 flex flex-wrap gap-2">
                {news.category && (
                    <span className="text-sm bg-gray-100 text-blue-600 px-2 py-1 rounded">
                        {news.category.name}
                    </span>
                )}
                {news.tags && news.tags.map((tag) => (
                    <span key={tag.id} className="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                        #{tag.name}
                    </span>
                ))}
            </div>
            <h1 className="text-2xl font-bold mb-2">{news.title}</h1>
            {/* Kapak fotoğrafı */}
            {news.image && (
                <img src={news.image} alt={news.title} className="rounded mb-4 max-h-72 w-full object-cover" />
            )}
            {/* Galeri */}
            {news.gallery && news.gallery.length > 0 && (
                <div className="mb-4">
                    <ImageGallery
                        items={news.gallery.map(img => ({
                            original: img.image,
                            thumbnail: img.image,
                        }))}
                        showFullscreenButton={true}
                        showPlayButton={true}
                        showBullets={true}
                    />
                </div>
            )}
            {/* Video player */}
            {news.video && <VideoPlayer url={news.video} />}

            <div className="mb-4 text-gray-700">{news.content}</div>
            <hr className="my-6" />
            <h3 className="text-xl font-semibold mb-3">Yorumlar</h3>

            {comments.length === 0 && <div>Henüz yorum yok.</div>}

            <ul className="mb-6">
                {/* Sadece en üst seviyedeki (parent olmayan) yorumları göster */}
                {comments.filter(c => !c.parent_id).map((c) => (
                    <CommentTree key={c.id} comment={c} depth={0} refresh={getComments} />
                ))}
            </ul>

            {/* Yeni yorum ekleme */}
            {user ? (
                <form onSubmit={handleAddComment} className="flex gap-2">
                    <input
                        value={comment}
                        onChange={e => setComment(e.target.value)}
                        placeholder="Yorumunuzu yazın..."
                        className="flex-1 border rounded px-3 py-2"
                    />
                    <button className="bg-blue-600 text-white px-4 rounded" type="submit">Ekle</button>
                </form>
            ) : (
                <div className="text-gray-600">Yorum eklemek için giriş yapınız.</div>
            )}
            {error && <div className="text-red-500 mt-2">{error}</div>}
        </div>
    );
}
