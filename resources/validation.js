if ( !window.gcd )
{
    window.gcd = {};
}

if ( !window.gcd.core )
{
    window.gcd.core = {};
}

if ( !window.gcd.core.validation )
{
    window.gcd.core.validation = {};
	window.gcd.core.validation.Scrolled = false;
}

window.gcd.core.validation.findValidationPlaceHolder = function( container, name )
{
    var children = container.children;

    for( var i = 0 ; i < children.length; i++ )
    {
        var node = children[ i ];

        if ( node.getAttribute( "name" ) == ( "ValidationPlaceHolder-" + name ) )
        {
            return node;
        }

        var childResult = window.gcd.core.validation.findValidationPlaceHolder( node, name );

        if ( childResult != false )
        {
            return childResult;
        }
    }

    return false;
};

window.gcd.core.validation.ValidationError = function( name, error )
{
    this.name = name;
    this.error = error;
    this.subErrors = [];

    this.applyToPlaceholders = function( validationHostContainer )
    {
        var placeHolder = window.gcd.core.validation.findValidationPlaceHolder( validationHostContainer, this.name );

        if ( placeHolder != false )
        {
			if ( !window.gcd.core.validation.Scrolled )
			{
				placeHolder.scrollIntoView();
				window.gcd.core.validation.Scrolled = true;
			}
            placeHolder.innerHTML = this.error;
        }

        for( var i in this.subErrors )
        {
            this.subErrors[ i ].applyToPlaceholders( validationHostContainer );
        }
    }
};

window.gcd.core.validation.BaseValidation = function( name, settings )
{
    this.name = name;
    this.settings = settings;
    this.failedMessage = "";

    this.validate = function( value )
    {

    };
}

window.gcd.core.validation.Validator = function( name )
{
    window.gcd.core.validation.BaseValidation.apply( this, arguments );

    this.validations = [];

    /**
     * Set to true to validate that all validations are correct. Set to false to validate that at least one
     * validation is correct.
     *
     * @type {boolean}
     */
    this.validateAll = true;

    this.validate = function( model )
    {
        var error = new window.gcd.core.validation.ValidationError( this.name, "The following errors occurred:" );

        var oneValid = false;
        var allValid = true;

        for( var v in this.validations )
        {
            var validation = this.validations[ v ];

            try
            {
                validation.validate( model[ validation.name ], model );

                oneValid = true;
            }
            catch( errorException )
            {
                error.subErrors[ error.subErrors.length ] = errorException;
                allValid = false;
            }
        }

        if ( !allValid && this.validateAll )
        {
            throw error;
        }

        if ( !oneValid && !this.validateAll )
        {
            throw error;
        }

        return true;
    }
}

window.gcd.core.validation.Validator.prototype = new window.gcd.core.validation.BaseValidation();
window.gcd.core.validation.Validator.prototype.constructor = window.gcd.core.validation.Validator;

window.gcd.core.validation.Validator.fromJson = function( json )
{
    var validator = new window.gcd.core.validation.Validator();

    validator.name = json.name;
    validator.validateAll = json.settings.validateAll;

    for( i in json.settings.validations )
    {
        var validationJson = json.settings.validations[i];
        var type = validationJson.type;
        var name = validationJson.name;
        var failedMessage = validationJson.failedMessage;
        var settings = validationJson.settings;

        var validationObject;

        if ( type == "validator" )
        {
            validationObject = window.gcd.core.validation.Validator.fromJson( settings );
            validationObject.name = name;
        }
        else
        {
            validationObject = new window.gcd.core.validation[ type ]( name, settings );
        }

        validationObject.failedMessage = failedMessage;
        validator.validations[ validator.validations.length ] = validationObject;
    }

    return validator;
}

window.gcd.core.validation.EqualTo = function( name, settings )
{
    window.gcd.core.validation.BaseValidation.apply( this, arguments );

    this.equalTo = settings.equalTo;

    this.validate = function( value )
    {
        if ( value != this.equalTo )
        {
            throw new window.gcd.core.validation.ValidationError( this.name, this.failedMessage )
        }

        return true;
    }
}

window.gcd.core.validation.EqualTo.prototype = new window.gcd.core.validation.BaseValidation();
window.gcd.core.validation.EqualTo.prototype.constructor = window.gcd.core.validation.EqualTo;

window.gcd.core.validation.EqualToModelProperty = function( name, settings )
{
	window.gcd.core.validation.BaseValidation.apply( this, arguments );

	this.propertyName = settings.propertyName;

	this.validate = function( value, model )
	{
		if ( value != model[ this.propertyName ] )
		{
			throw new window.gcd.core.validation.ValidationError( this.name, this.failedMessage )
		}

		return true;
	}
}

window.gcd.core.validation.EqualToModelProperty.prototype = new window.gcd.core.validation.BaseValidation();
window.gcd.core.validation.EqualToModelProperty.prototype.constructor = window.gcd.core.validation.EqualToModelProperty;

window.gcd.core.validation.HasValue = function( name, settings )
{
    window.gcd.core.validation.BaseValidation.apply( this, arguments );

    this.validate = function( value )
    {
        if ( value === null || value == "" || value == 0 )
        {
            throw new window.gcd.core.validation.ValidationError( this.name, this.failedMessage )
        }

        return true;
    }
}

window.gcd.core.validation.EqualTo.prototype = new window.gcd.core.validation.BaseValidation();
window.gcd.core.validation.EqualTo.prototype.constructor = window.gcd.core.validation.EqualTo;