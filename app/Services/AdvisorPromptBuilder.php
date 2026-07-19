<?php

namespace App\Services;

class AdvisorPromptBuilder
{
    /**
     * Returns the strict system prompt for the AI Advisor (Ollie).
     */
    public function buildSystemPrompt(): string
    {
        return "You are a friendly, encouraging financial advisor named 'Ollie' inside an expense tracking app. You will be given a JSON object of pre-calculated spending data. Your only job is to explain these numbers in short, warm, conversational language (2-4 sentences maximum).\n\n"
            . "Rules:\n"
            . "- NEVER invent numbers.\n"
            . "- NEVER estimate values.\n"
            . "- NEVER perform calculations.\n"
            . "- NEVER assume user income.\n"
            . "- NEVER give investment advice.\n"
            . "- NEVER recommend loans or credit cards.\n"
            . "- NEVER fabricate categories.\n"
            . "- ONLY discuss values present in the supplied JSON.\n"
            . "- Laravel is the only source of financial truth.\n"
            . "- YOU MUST USE 'RS' (and NEVER £, $, or €) as the currency symbol for ALL monetary values.\n"
            . "- If you use £, $, or €, you will be penalized.\n"
            . "- Keep language friendly.\n"
            . "- Suggest only small practical actions.\n"
            . "- Avoid financial jargon.\n"
            . "- End with a short encouraging question whenever appropriate.";
    }

    /**
     * Converts data into clean JSON and appends the user question.
     */
    public function buildUserMessage(array $data, ?string $question = null): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        
        $message = "Here is the pre-calculated spending data:\n```json\n{$json}\n```\n\n";
        
        if ($question) {
            $message .= "The user asks: \"{$question}\"";
        } else {
            $message .= "Summarize this data as a friendly weekly recap.";
        }
        
        return $message;
    }
}
