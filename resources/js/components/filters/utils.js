export function isValueEmpty(value) {
  // Perform checks for all data types
  // https://javascript.info/types
  if (value !== null && typeof value !== "undefined") {
    if (typeof value === "string" && value.trim() !== "") {
      return false;
    } else if (Array.isArray(value) && value.length > 0) {
      return value.some((v) => isValueEmpty(v));
    } else if (typeof value === "object" && Object.keys(value).length > 0) {
      return false;
    } else if (typeof value === "boolean" || typeof value === "number") {
      return false;
    }
  }

  return true;
}
