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

type Comment = {
    id: number;
    content: string;
    user: { id: number; name: string };
    created_at: string;
};
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
    const [comments, setComments] = useState<Comment[]>([]);
    const [comment, setComment] = useState("");
    const [error, setError] = useState("");
    const { user } = useAuth();


    const [editCommentId, setEditCommentId] = useState<number | null>(null);
    const [editContent, setEditContent] = useState("");

    useEffect(() => {
        fetchNewsDetail(Number(id)).then(setNews);
        fetchComments(Number(id)).then(setComments);
    }, [id]);


    const handleAddComment = async (e: React.FormEvent) => {
        e.preventDefault();
        setError("");
        if (!comment.trim()) return;
        try {
            await addComment(Number(id), comment);
            setComment("");
            fetchComments(Number(id)).then(setComments);
        } catch {
            setError("Yorum eklenemedi. Giriş yaptığınızdan emin olun.");
        }
    };


    const handleEditComment = (c: Comment) => {
        setEditCommentId(c.id);
        setEditContent(c.content);
    };


    const handleSaveEdit = async () => {
        if (!editContent.trim() || !editCommentId) return;
        await updateComment(editCommentId, editContent);
        setEditCommentId(null);
        setEditContent("");
        fetchComments(Number(id)).then(setComments);
    };


    const handleDeleteComment = async (commentId: number) => {
        if (!window.confirm("Yorumu silmek istediğinize emin misiniz?")) return;
        await deleteComment(commentId);
        fetchComments(Number(id)).then(setComments);
    };

   
    const handleReportComment = async (commentId: number) => {
        await reportComment(commentId);
        alert("Yorum şikayet edildi. Teşekkürler.");
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
            {editCommentId && (
                <form
                    onSubmit={e => {
                        e.preventDefault();
                        handleSaveEdit();
                    }}
                    className="mb-4 flex gap-2"
                >
                    <input
                        className="flex-1 border px-2 py-1 rounded"
                        value={editContent}
                        onChange={e => setEditContent(e.target.value)}
                        autoFocus
                    />
                    <button className="bg-blue-600 text-white px-3 rounded" type="submit">
                        Kaydet
                    </button>
                    <button
                        className="bg-gray-200 px-2 rounded"
                        type="button"
                        onClick={() => setEditCommentId(null)}
                    >
                        Vazgeç
                    </button>
                </form>
            )}

            {comments.length === 0 && <div>Henüz yorum yok.</div>}

            <ul className="mb-6">
                {comments.map((c) => (
                    <li key={c.id} className="mb-3 p-2 border-b flex justify-between items-start">
                        <div>
                            <div className="font-medium">{c.user?.name || "Anonim"}</div>
                            <div className="text-gray-800">{c.content}</div>
                            <div className="text-xs text-gray-500">{new Date(c.created_at).toLocaleString()}</div>
                        </div>
                        <div className="flex flex-col gap-1 ml-2">
                            {/* Yorumun sahibi ise düzenle/sil */}
                            {user && c.user && user.id === c.user.id && (
                                <>
                                    <button
                                        className="text-blue-600 text-xs"
                                        onClick={() => handleEditComment(c)}
                                    >
                                        Düzenle
                                    </button>
                                    <button
                                        className="text-red-600 text-xs"
                                        onClick={() => handleDeleteComment(c.id)}
                                    >
                                        Sil
                                    </button>
                                </>
                            )}
                            {/* Yorumun sahibi değilse şikayet */}
                            {user && (!c.user || user.id !== c.user.id) && (
                                <button
                                    className="text-yellow-600 text-xs"
                                    onClick={() => handleReportComment(c.id)}
                                >
                                    Şikayet Et
                                </button>
                            )}
                        </div>
                    </li>
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
