export type AiChatMessageID = number;

export type AiChatMessageStatus = 'completed' | 'pending' | 'failed';
export type AiChatMessageRole = 'system' | 'user' | 'assistant' | 'function' | 'tool';

export interface AiChatMessage {
    id: AiChatMessageID;
    user_id: number;
    status: typeof STATUSES[keyof typeof STATUSES];
    role: typeof ROLES[keyof typeof ROLES];
    content: string;
}

export const STATUSES = {
    COMPLETED: 'completed',
    PENDING: 'pending',
    FAILED: 'failed',
} as const;

export const ROLES = {
    SYSTEM: 'system',
    USER: 'user',
    ASSISTANT: 'assistant',
    FUNCTION: 'function',
    TOOL: 'tool'
} as const;