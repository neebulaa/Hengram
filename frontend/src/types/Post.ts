import User from './User.ts';
import PostAttachment from './PostAttachment';
type Post = {
    id: number;
    caption: string;
    created_at: string;
    updated_at: string;
    attachments?: PostAttachment[];
    user?: User;
    user_id: number;
}

export default Post;