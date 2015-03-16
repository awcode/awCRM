{!! Form::open(array('url'=>'#', 'class'=>'form-signup')) !!}
	<h2 class="form-signup-heading">Staff</h2>

	<ul>
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>

	{!! Form::text('firstname', $staff['firstname'], array('class'=>' form-control', 'placeholder'=>'First Name')) !!}
	{!! Form::text('lastname', $staff['lastname'], array('class'=>' form-control', 'placeholder'=>'Last Name')) !!}
	{!! Form::text('email', $staff['email'], array('class'=>' form-control', 'placeholder'=>'Email Address')) !!}
	{!! Form::password('password', array('class'=>'form-control', 'placeholder'=>'Password')) !!}
	{!! Form::password('password_confirmation', array('class'=>' form-control', 'placeholder'=>'Confirm Password')) !!}

	{!! Form::submit('Register', array('class'=>'btn btn-large btn-primary btn-block'))!!}
{!! Form::close() !!}
