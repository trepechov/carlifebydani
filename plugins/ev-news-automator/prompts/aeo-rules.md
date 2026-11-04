<!--
Shared AEO (answer-engine optimisation) writing rules for EV News Automator.
Pulled into analysis-system.md and podcast-summary.md through the {{AEO_RULES}} placeholder,
so the sheet description and the podcast «Резюме» always follow the same rules.
Edit here; HTML comments like this one are stripped before the prompt is sent.
Rationale and measurements: ../docs/AEO_CONTENT_GUIDELINES.md
-->
Writing rules (AEO — readers AND AI answer engines quote single sentences out of context):
- Answer first: the first sentence states what happened on its own, as a fact.
- Every sentence names its subject explicitly (company, brand, model, person). Never "it", "the company", "the car", "той", "тя", "компанията".
- Use only facts present in the source text. Never invent figures, dates, prices or quotes. No opinions, no filler.
- Absolute dates only. Never "this week", "yesterday", "on Monday", "тази седмица", "вчера". Resolve relative dates from the publication date given in the input; if it is unknown and the date cannot be resolved, leave the date out.
- Attribution: when a fact is a company claim, a report or an estimate, say so ("според Tesla", "съобщи Reuters", "по данни на …").
- Keep prices in the original currency; do not convert them. Use compact forms ("41 900 AUD", "$66 200"). A bare "$" or "долара" means US dollars only — for any other dollar write the code (AUD, CAD, NZD, SGD), e.g. "58 990 AUD", never "$58 990" or "59 000 долара" for an Australian price.
- Distances and speeds in imperial units: keep the original value and add the metric value in brackets, e.g. "428 мили (около 689 км)".

Audience and market rules (readers are in Bulgaria; Bulgarian and European news matters most):
- If the news is tied to one market (price, launch, subsidy, recall, sales figure, regulation), name that market in Bulgarian in the title or the first sentence ("в САЩ", "в Австралия", "в Германия") — a reader who only sees the title and first sentence must know whether the story applies to them. A currency is not enough to name the market.
- For news from outside Europe, state whether it applies to Europe/Bulgaria ONLY if the source says so. Never guess or imply European availability. If the source says nothing about Europe/Bulgaria, say nothing about it — do not write that the information is missing.
- Global news (company results, technology, worldwide reveals) gets no market label.
