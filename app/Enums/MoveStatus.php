<?php

declare(strict_types=1);

namespace App\Enums;

enum MoveStatus: string
{
    case Draft = 'draft';
    case InSurvey = 'in_survey';
    case PendingSignoff = 'pending_signoff';
    case OriginSigned = 'origin_signed';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InSurvey => 'In Survey',
            self::PendingSignoff => 'Pending Customer Sign-Off',
            self::OriginSigned => 'Origin Signed',
            self::InTransit => 'In Transit',
            self::Delivered => 'Delivered / Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 border-slate-300',
            self::InSurvey => 'bg-blue-50 text-blue-700 border-blue-200',
            self::PendingSignoff => 'bg-amber-50 text-amber-800 border-amber-300',
            self::OriginSigned => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::InTransit => 'bg-purple-50 text-purple-700 border-purple-200',
            self::Delivered => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Cancelled => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }

    public function canEditInventory(): bool
    {
        return in_array($this, [self::Draft, self::InSurvey, self::PendingSignoff], true);
    }

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Draft => in_array($target, [self::InSurvey, self::Cancelled], true),
            self::InSurvey => in_array($target, [self::PendingSignoff, self::OriginSigned, self::Cancelled], true),
            self::PendingSignoff => in_array($target, [self::InSurvey, self::OriginSigned, self::Cancelled], true),
            self::OriginSigned => in_array($target, [self::InTransit, self::InSurvey], true), // InSurvey via unlock revision
            self::InTransit => in_array($target, [self::Delivered], true),
            self::Delivered => false,
            self::Cancelled => false,
        };
    }
}
