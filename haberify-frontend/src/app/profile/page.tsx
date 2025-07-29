'use client';

import { useEffect, useState } from "react";
import { fetchProfile, updateProfile } from "@/services/authApi";
import { useAuth } from "@/contexts/AuthContext";

export default function ProfilePage() {
    const { user, setUser } = useAuth();
    const [loading, setLoading] = useState(true);
    const [profile, setProfile] = useState<any>(null);
    const [name, setName] = useState("");
    const [avatar, setAvatar] = useState<File | null>(null);
    const [message, setMessage] = useState("");
    const [updating, setUpdating] = useState(false);

    useEffect(() => {
        fetchProfile().then(profile => {
            setProfile(profile);
            setName(profile.name || "");
            setLoading(false);
        });
    }, []);

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) setAvatar(e.target.files[0]);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setUpdating(true);
        setMessage("");
        try {
            const updated = await updateProfile({ name, avatar });
            setProfile(updated);
            setUser && setUser(updated);
            setMessage("Profil başarıyla güncellendi!");
            setAvatar(null);
        } catch {
            setMessage("Profil güncellenemedi!");
        } finally {
            setUpdating(false);
        }
    };

    if (loading) return <div>Yükleniyor...</div>;
    if (!profile) return <div>Profil bulunamadı!</div>;

    return (
        <div className="max-w-lg mx-auto bg-white p-8 rounded-xl shadow mt-8">
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
                    {profile.avatar_url && (
                        <img src={profile.avatar_url} alt="Profil Foto" className="h-16 w-16 mt-2 rounded-full object-cover" />
                    )}
                </div>
                <button className="w-full bg-blue-600 text-white py-2 rounded font-semibold" type="submit" disabled={updating}>
                    {updating ? "Kaydediliyor..." : "Kaydet"}
                </button>
                {message && <div className="mt-2 text-center text-green-600">{message}</div>}
            </form>
        </div>
    );
}
