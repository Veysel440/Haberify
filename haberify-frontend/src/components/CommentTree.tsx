'use client';

import { useState } from "react";
import { useAuth } from "@/contexts/AuthContext";
import { addComment, deleteComment, updateComment, reportComment } from "@/services/commentApi";

export type CommentType = {
    id: number;
    content: string;
    user: { id: number; name: string };
    created_at: string;
    parent_id?: number | null;
    replies?: CommentType[];
    news_id?: number;
};

type Props = {
    comment: CommentType;
    depth?: number;
    refresh: () => void;
};

export default function CommentTree({ comment, depth = 0, refresh }: Props) {
    const { user } = useAuth();


    const [showReplyBox, setShowReplyBox] = useState(false);
    const [reply, setReply] = useState("");
    const [loading, setLoading] = useState(false);


    const [isEditing, setIsEditing] = useState(false);
    const [editContent, setEditContent] = useState(comment.content);

    const handleReply = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        try {
            await addComment(comment.news_id!, reply, comment.id);
            setReply("");
            setShowReplyBox(false);
            refresh();
        } finally {
            setLoading(false);
        }
    };

    const handleSaveEdit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!editContent.trim()) return;
        await updateComment(comment.id, editContent);
        setIsEditing(false);
        refresh();
    };

    const handleDelete = async () => {
        if (!window.confirm("Yorumu silmek istediğinize emin misiniz?")) return;
        await deleteComment(comment.id);
        refresh();
    };

    const handleReport = async () => {
        await reportComment(comment.id);
        alert("Yorum şikayet edildi. Teşekkürler.");
    };

    return (
        <div className="mb-2" style={{ marginLeft: depth * 24 }}>
            <div className="bg-gray-50 rounded px-3 py-2 shadow-sm flex justify-between items-start">
                <div>
                    <div className="font-semibold text-sm">{comment.user?.name || "Anonim"}</div>
                    {isEditing ? (
                        <form onSubmit={handleSaveEdit} className="flex gap-2">
                            <input
                                className="flex-1 border px-2 py-1 rounded"
                                value={editContent}
                                onChange={e => setEditContent(e.target.value)}
                                autoFocus
                            />
                            <button className="bg-blue-600 text-white px-2 rounded" type="submit">
                                Kaydet
                            </button>
                            <button
                                className="bg-gray-200 px-2 rounded"
                                type="button"
                                onClick={() => setIsEditing(false)}
                            >
                                Vazgeç
                            </button>
                        </form>
                    ) : (
                        <div className="text-gray-800">{comment.content}</div>
                    )}
                    <div className="text-xs text-gray-400">{new Date(comment.created_at).toLocaleString()}</div>
                </div>
                <div className="flex flex-col gap-1 ml-2">
                    {user && comment.user && user.id === comment.user.id && !isEditing && (
                        <>
                            <button className="text-blue-600 text-xs" onClick={() => setIsEditing(true)}>
                                Düzenle
                            </button>
                            <button className="text-red-600 text-xs" onClick={handleDelete}>
                                Sil
                            </button>
                        </>
                    )}
                    {user && (!comment.user || user.id !== comment.user.id) && (
                        <button className="text-yellow-600 text-xs" onClick={handleReport}>
                            Şikayet Et
                        </button>
                    )}
                    {user && depth < 2 && !isEditing && (
                        <button
                            className="text-blue-600 text-xs mt-1"
                            onClick={() => setShowReplyBox(v => !v)}
                        >
                            {showReplyBox ? "Vazgeç" : "Yanıtla"}
                        </button>
                    )}
                </div>
            </div>
            {showReplyBox && (
                <form onSubmit={handleReply} className="flex gap-2 mt-1">
                    <input
                        value={reply}
                        onChange={e => setReply(e.target.value)}
                        placeholder="Cevabınız..."
                        className="flex-1 border rounded px-2 py-1"
                    />
                    <button
                        className="bg-blue-600 text-white px-3 rounded text-sm"
                        disabled={loading}
                        type="submit"
                    >
                        Gönder
                    </button>
                </form>
            )}
            {Array.isArray(comment.replies) && comment.replies.length > 0 &&
                comment.replies.map((r) => (
                    <CommentTree key={r.id} comment={r} depth={depth + 1} refresh={refresh} />
                ))
            }
        </div>
    );
}
