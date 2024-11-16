/**
 * Custom hook for managing form errors.
 * @param {object} form - The form object containing error information.
 * @returns {object} - An object with functions to handle form errors.
 */
const useFormError = (form) => {
  /**
   * Resets the error for a specific property.
   * @param {string} propertyName - The name of the property to reset the error for.
   */
  const clearError = (propertyName) => {
    delete form.errors[propertyName];
  };

  /**
   * Gets the error message for a specific property.
   * @param {string} propertyName - The name of the property to get the error message for.
   * @returns {string | undefined} - The error message or undefined if no error.
   */
  const getError = (propertyName) => {
    return form.errors[propertyName];
  };

  /**
   * Checks if a specific property has an error.
   * @param {string | RegExp} propertyKey - The name of the property or a RegExp to match property names.
   * @returns {boolean} - True if there is an error, otherwise false.
   */
  const hasError = (propertyKey) => {
    if (propertyKey instanceof RegExp) {
      // Check if any property matches the RegExp.
      for (const key in form.errors) {
        if (propertyKey.test(key)) {
          return true;
        }
      }
    } else {
      // Check if the specified property has an error.
      return !!form.errors[propertyKey];
    }
    return false;
  };

  /**
   * Provides error attributes for a specific property.
   * @param {string} propertyName - The name of the property to generate error attributes for.
   * @returns {object} - An object with error-related attributes.
   */
  const errorAttributes = (propertyName) => {
    if (!hasError(propertyName)) {
      return {};
    }

    return {
      class: 'show-details',
      errorMessages: getError(propertyName),
      onFocus: () => clearError(propertyName),
    };
  };

  return {
    clearError,
    hasError,
    getError,
    errorAttributes,
  };
};

export default useFormError;
