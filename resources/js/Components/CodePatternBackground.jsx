import * as React from "react";
import { cn } from "@/lib/utils";

/**
 * CodePatternBackground - A decorative background with code-themed patterns
 * Features syntax elements, brackets, and code symbols
 */
export function CodePatternBackground({ className, children }) {
    return (
        <div className={cn("relative min-h-svh overflow-hidden", className)}>
            {/* Base gradient background */}
            <div className="absolute inset-0 bg-gradient-to-br from-muted via-muted to-muted/80" />

            {/* Code pattern overlay */}
            <div className="absolute inset-0 opacity-[0.03] dark:opacity-[0.05]">
                <svg
                    className="absolute inset-0 h-full w-full"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <defs>
                        <pattern
                            id="code-pattern"
                            x="0"
                            y="0"
                            width="100"
                            height="100"
                            patternUnits="userSpaceOnUse"
                        >
                            {/* Curly braces */}
                            <text
                                x="10"
                                y="20"
                                className="fill-foreground"
                                style={{ fontSize: "14px", fontFamily: "monospace" }}
                            >
                                {"{"}
                            </text>
                            <text
                                x="80"
                                y="80"
                                className="fill-foreground"
                                style={{ fontSize: "14px", fontFamily: "monospace" }}
                            >
                                {"}"}
                            </text>
                            {/* Angle brackets */}
                            <text
                                x="50"
                                y="40"
                                className="fill-foreground"
                                style={{ fontSize: "12px", fontFamily: "monospace" }}
                            >
                                {"</>"}
                            </text>
                            {/* Parentheses */}
                            <text
                                x="25"
                                y="60"
                                className="fill-foreground"
                                style={{ fontSize: "10px", fontFamily: "monospace" }}
                            >
                                {"()"}
                            </text>
                            {/* Square brackets */}
                            <text
                                x="70"
                                y="25"
                                className="fill-foreground"
                                style={{ fontSize: "10px", fontFamily: "monospace" }}
                            >
                                {"[]"}
                            </text>
                            {/* Semicolon */}
                            <text
                                x="45"
                                y="90"
                                className="fill-foreground"
                                style={{ fontSize: "12px", fontFamily: "monospace" }}
                            >
                                {";"}
                            </text>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#code-pattern)" />
                </svg>
            </div>

            {/* Floating code snippets - decorative elements */}
            <div className="absolute inset-0 overflow-hidden pointer-events-none">
                {/* Top left code block */}
                <div className="absolute -top-4 -left-4 rotate-[-8deg] opacity-[0.04] dark:opacity-[0.06]">
                    <pre className="text-xs font-mono text-foreground leading-relaxed">
{`function share() {
  const snippet = {
    code: "...",
    lang: "js"
  };
  return snippet;
}`}
                    </pre>
                </div>

                {/* Top right code block */}
                <div className="absolute top-20 -right-8 rotate-[6deg] opacity-[0.04] dark:opacity-[0.06]">
                    <pre className="text-xs font-mono text-foreground leading-relaxed">
{`import { Code } from
  "snippet-share";

export default App;`}
                    </pre>
                </div>

                {/* Bottom left code block */}
                <div className="absolute bottom-32 -left-12 rotate-[4deg] opacity-[0.04] dark:opacity-[0.06]">
                    <pre className="text-xs font-mono text-foreground leading-relaxed">
{`const teams = await
  getTeams();
teams.map(t =>
  t.snippets);`}
                    </pre>
                </div>

                {/* Bottom right code block */}
                <div className="absolute -bottom-8 right-20 rotate-[-5deg] opacity-[0.04] dark:opacity-[0.06]">
                    <pre className="text-xs font-mono text-foreground leading-relaxed">
{`<Snippet
  language="py"
  theme="dark"
/>`}
                    </pre>
                </div>

                {/* Center left */}
                <div className="absolute top-1/3 -left-16 rotate-[12deg] opacity-[0.03] dark:opacity-[0.05]">
                    <pre className="text-[10px] font-mono text-foreground">
{`// Share code
// With teams`}
                    </pre>
                </div>

                {/* Center right */}
                <div className="absolute top-1/2 -right-20 rotate-[-10deg] opacity-[0.03] dark:opacity-[0.05]">
                    <pre className="text-[10px] font-mono text-foreground">
{`/* syntax
   highlight */`}
                    </pre>
                </div>
            </div>

            {/* Gradient orbs for depth */}
            <div className="absolute top-0 left-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl" />
            <div className="absolute bottom-0 right-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl" />

            {/* Content */}
            <div className="relative z-10">{children}</div>
        </div>
    );
}

export default CodePatternBackground;
