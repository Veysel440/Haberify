'use client';

import { useEffect, useState } from "react";
import { fetchProfile, updateProfile, changePassword } from "@/services/authApi";
import { useAuth } from "@/contexts/AuthContext";

export default function ProfilePage() {
    const { user, setUser } = useAuth();
    const [loading, setLoading] = useState(true);
    const [profile, setProfile] = useState<any>(null);
    const [name, setName] = useState("");
    const [avatar, setAvatar] = useState<File | null>(null);
    const [avatarPreview, setAvatarPreview] = useState<string | null>(null);
    const [message, setMessage] = useState<{ text: string; error?: boolean }>({ text: "" });
    const [updating, setUpdating] = useState(false);

    const [showPasswordForm, setShowPasswordForm] = useState(false);
    const [oldPassword, setOldPassword] = useState("");
    const [newPassword, setNewPassword] = useState("");
    const [passMsg, setPassMsg] = useState<{ text: string; error?: boolean }>({ text: "" });
    const [passLoading, setPassLoading] = useState(false);

    useEffect(() => {
        fetchProfile().then(profile => {
            setProfile(profile);
            setName(profile.name || "");
            setLoading(false);
        });
    }, []);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setAvatar(e.target.files[0]);
            setAvatarPreview(URL.createObjectURL(e.target.files[0]));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setUpdating(true);
        setMessage({ text: "" });
        try {
            const updated = await updateProfile({ name, avatar });
            setProfile(updated);
            setUser && setUser(updated);
            setMessage({ text: "Profil başarıyla güncellendi!" });
            setAvatar(null);
            setAvatarPreview(null);
        } catch {
            setMessage({ text: "Profil güncellenemedi!", error: true });
        } finally {
            setUpdating(false);
        }
    };

    const handlePasswordChange = async (e: React.FormEvent) => {
        e.preventDefault();
        setPassLoading(true);
        setPassMsg({ text: "" });
        try {
            await changePassword({ old_password: oldPassword, new_password: newPassword });
            setPassMsg({ text: "Şifre değiştirildi." });
            setOldPassword(""); setNewPassword("");
            setShowPasswordForm(false);
        } catch (err: any) {
            setPassMsg({ text: err?.response?.data?.message || "Şifre değiştirilemedi.", error: true });
        } finally {
            setPassLoading(false);
        }
    };

    if (loading) return <div>Yükleniyor...</div>;
    if (!profile) return <div>Profil bulunamadı!</div>;

    return (
        <div className="max-w-lg mx-auto bg-white dark:bg-gray-900 p-8 rounded-xl shadow mt-8">
            <h2 className="text-xl font-bold mb-4">Profilim</h2>
            <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                    <label className="block mb-2 font-medium">Adınız:</label>
                    <input
                        type="text"
                        className="w-full border p-2 rounded"
                        value={name}
                        onChange={e => setName(e.target.value)}
                        disabled={updating}
                    />
                </div>
                <div>
                    <label className="block mb-2 font-medium">E-mail:</label>
                    <input
                        type="email"
                        className="w-full border p-2 rounded bg-gray-100"
                        value={profile.email}
                        disabled
                    />
                </div>
                <div>
                    <label className="block mb-2 font-medium">Profil Fotoğrafı:</label>
                    <input type="file" accept="image/*" onChange={handleFileChange} />
                    <div className="flex gap-3 items-center mt-2">
                        {/* Yeni seçilen fotoğraf önizlemesi */}
                        {avatarPreview && (
                            <img src={avatarPreview} alt="Yeni Profil" className="h-16 w-16 rounded-full object-cover border-2 border-blue-400" />
                        )}
                        {/* Sunucudaki mevcut fotoğraf */}
                        {profile.avatar_url && !avatarPreview && (
                            <img src={profile.avatar_url} alt="Profil Foto" className="h-16 w-16 rounded-full object-cover" />
                        )}
                    </div>
                </div>
                <button
                    className="w-full bg-blue-600 text-white py-2 rounded font-semibold"
                    type="submit"
                    disabled={updating}
                >
                    {updating ? "Kaydediliyor..." : "Kaydet"}
                </button>
                {message.text && (
                    <div className={`mt-2 text-center ${message.error ? "text-red-600" : "text-green-600"}`}>
                        {message.text}
                    </div>
                )}
            </form>
            <hr className="my-8" />
            {/* Şifre değiştir bölümü */}
            <button
                onClick={() => setShowPasswordForm(s => !s)}
                className="w-full bg-gray-200 text-gray-800 py-2 rounded font-semibold mb-2"
            >
                {showPasswordForm ? "Şifre Değiştir'i Kapat" : "Şifre Değiştir"}
            </button>
            {showPasswordForm && (
                <form onSubmit={handlePasswordChange} className="space-y-4 mt-2">
                    <div>
                        <input
                            type="password"
                            placeholder="Eski şifre"
                            className="w-full border p-2 rounded"
                            value={oldPassword}
                            onChange={e => setOldPassword(e.target.value)}
                            required
                        />
                    </div>
                    <div>
                        <input
                            type="password"
                            placeholder="Yeni şifre"
                            className="w-full border p-2 rounded"
                            value={newPassword}
                            onChange={e => setNewPassword(e.target.value)}
                            required
                        />
                    </div>
                    <button
                        type="submit"
                        className="w-full bg-orange-600 text-white py-2 rounded font-semibold"
                        disabled={passLoading}
                    >
                        {passLoading ? "Değiştiriliyor..." : "Şifreyi Değiştir"}
                    </button>
                    {passMsg.text && (
                        <div className={`mt-2 text-center ${passMsg.error ? "text-red-600" : "text-green-600"}`}>
                            {passMsg.text}
                        </div>
                    )}
                </form>
            )}
        </div>
    );
}
