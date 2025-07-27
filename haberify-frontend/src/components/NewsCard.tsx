import Link from "next/link";

type NewsCardProps = {
    id: number;
    title: string;
    excerpt: string;
    image?: string | null;
};

const NewsCard = ({ id, title, excerpt, image }: NewsCardProps) => (
    <div className="bg-white rounded-xl shadow p-4 flex gap-4 mb-4">
        {image && (
            <img src={image} alt={title} className="w-32 h-24 object-cover rounded-lg" />
        )}
        <div>
            <h2 className="font-semibold text-lg mb-2">
                <Link href={`/news/${id}`} className="hover:underline text-blue-600">{title}</Link>
            </h2>
            <p className="text-gray-700">{excerpt}</p>
        </div>
    </div>
);

export default NewsCard;
