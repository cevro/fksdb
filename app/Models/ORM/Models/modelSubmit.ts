export interface ModelSubmit {
    submitId: number | null;
    name: string;
    deadline: string | null;
    taskId: number;
    isQuiz: boolean;
    disabled: boolean;
}
