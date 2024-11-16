import { DateTime, Duration } from 'luxon';

export function format(timestamp, format = DateTime.DATE_FULL) {
  const date = DateTime.fromISO(timestamp);
  if (typeof format === 'string') {
    return date.toFormat(format);
  }
  return date.toLocaleString(format);
}

export function date(timestamp, format = DateTime.DATE_FULL) {
  return format(timestamp);
}

export function datetime(timestamp, format = DateTime.DATETIME_MED) {
  const date = DateTime.fromISO(timestamp);
  return date.toLocaleString(format);
}

export function time(timestamp, format = DateTime.TIME_SIMPLE) {
  const date = DateTime.fromISO(timestamp);
  return date.toLocaleString(format);
}

export function human(timestamp, options = {}) {
  let { reverse = false, parts = 2, from = null } = options;
  const date = DateTime.fromISO(timestamp);
  const units = ['months', 'days', 'hours', 'minutes'];

  from = from == null ? DateTime.now() : DateTime.fromISO(from);

  const diff = reverse ? date.diff(from, units) : from.diff(date, units);
  return toHuman(Duration.fromObject(diff.toObject()), parts);
}

function toHuman(dur, parts = 2) {
  const units = ['years', 'months', 'days', 'hours', 'minutes', 'seconds'];

  const entries = Object.entries(
    dur
      .shiftTo(...units)
      .normalize()
      .toObject()
  )
    .filter(([_unit, amount]) => amount > 0)
    .map(([_unit, amount]) => [_unit, Math.ceil(amount)]);

  const limitedEntries = entries.slice(0, parts);

  const dur2 = Duration.fromObject(
    limitedEntries.length === 0
      ? { [units[parts - 1]]: 0 }
      : Object.fromEntries(limitedEntries)
  );

  return dur2.toHuman();
}

export default {
  format,
  date,
  datetime,
  time,
  toHuman: human,
};
