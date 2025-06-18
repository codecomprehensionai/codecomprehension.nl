import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

const placeholderQuestions = [
  {
    id: 1,
    title: "Understanding Basic Concepts",
    description: "Test your knowledge of fundamental concepts",
    difficulty: "Easy",
    timeLimit: 15,
  },
  {
    id: 2,
    title: "Problem Solving Challenge",
    description: "Apply your knowledge to solve real-world problems",
    difficulty: "Medium",
    timeLimit: 30,
  },
  {
    id: 3,
    title: "Advanced Analysis",
    description: "Deep dive into complex scenarios and analysis",
    difficulty: "Hard",
    timeLimit: 45,
  },
];

export default function TeacherQuestionsPage() {
  return (
    <div className="container mx-auto py-8">
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">Assignment Questions</h1>
        <Button>Create New Question</Button>
      </div>

      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        {placeholderQuestions.map((question) => (
          <Card key={question.id}>
            <CardHeader>
              <CardTitle>{question.title}</CardTitle>
              <CardDescription>{question.description}</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-2">
                <p className="text-sm text-muted-foreground">
                  Difficulty: <span className="font-medium">{question.difficulty}</span>
                </p>
                <p className="text-sm text-muted-foreground">
                  Time Limit: <span className="font-medium">{question.timeLimit} minutes</span>
                </p>
                <div className="flex gap-2 mt-4">
                  <Button variant="outline" size="sm">Edit</Button>
                  <Button variant="outline" size="sm" className="text-red-500">Delete</Button>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}