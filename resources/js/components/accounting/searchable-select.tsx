import { useEffect, useMemo, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

type Option = {
    value: string;
    label: string;
};

type Props = {
    value: string;
    options: Option[];
    placeholder: string;
    onChange: (value: string) => void;
    className?: string;
};

export function SearchableSelect({ value, options, placeholder, onChange, className }: Props) {
    const selected = options.find((option) => option.value === value);
    const [query, setQuery] = useState(selected?.label ?? '');
    const [open, setOpen] = useState(false);
    const rootRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const closeWhenOutside = (event: MouseEvent) => {
            if (rootRef.current && !rootRef.current.contains(event.target as Node)) {
                setOpen(false);

                if (!selected && value === '') {
                    setQuery('');
                }
            }
        };

        document.addEventListener('mousedown', closeWhenOutside);

        return () => document.removeEventListener('mousedown', closeWhenOutside);
    }, [selected, value]);

    const filtered = useMemo(() => {
        const normalized = query.toLowerCase().trim();

        return options
            .filter((option) => option.label.toLowerCase().includes(normalized))
            .slice(0, 50);
    }, [options, query]);

    return (
        <div ref={rootRef} className={cn('relative', className)}>
            <Input
                value={query}
                placeholder={placeholder}
                onFocus={() => setOpen(true)}
                onKeyDown={(event) => {
                    if (event.key === 'Escape') {
                        setOpen(false);
                    }
                }}
                onChange={(event) => {
                    setQuery(event.target.value);
                    setOpen(true);

                    if (event.target.value === '') {
                        onChange('');
                    }
                }}
            />
            {open && (
                <div className="absolute z-50 mt-1 max-h-64 w-full overflow-auto rounded-md border bg-background p-1 shadow-md">
                    {filtered.length === 0 ? (
                        <div className="px-2 py-2 text-sm text-muted-foreground">No results</div>
                    ) : (
                        filtered.map((option) => (
                            <Button
                                key={option.value}
                                type="button"
                                variant="ghost"
                                className="h-auto w-full justify-start px-2 py-1.5 text-left"
                                onMouseDown={(event) => event.preventDefault()}
                                onClick={() => {
                                    onChange(option.value);
                                    setQuery(option.label);
                                    setOpen(false);
                                }}
                            >
                                {option.label}
                            </Button>
                        ))
                    )}
                </div>
            )}
        </div>
    );
}
