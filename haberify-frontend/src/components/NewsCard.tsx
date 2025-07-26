import { FC } from 'react';

type NewsCardProps = {
    title: string;
    excerpt: string;
    image?: string | null;
};

const NewsCard: FC<NewsCardProps> = ({ title, excerpt, image }) => (
    <div className="bg-white rounded-xl shadow p-4 flex gap-4 mb-4">
        {image && (
            <img src={image} alt={title} className="w-32 h-24 object-cover rounded-lg" />
        )}
        <div>
            <h2 className="font-semibold text-lg mb-2">{title}</h2>
            <p className="text-gray-700">{excerpt}</p>
        </div>
    </div>
);

export default NewsCard;
