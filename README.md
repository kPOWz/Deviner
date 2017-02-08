#GUS


##Style Guide

http://kpowz.github.io/gus-bootstrap/
source: https://github.com/kPOWz/gus-bootstrap


##Dependency Management

Composer


##Deployment

To deploy GUS each environment has a bare repo to <pre> git push</pre> to. The bare repo, having no working copy of the source, is equipped with a post-recieve hook listening for pushes.  When the push completes, the post-recieve hook issues a <pre>git pull</pre> latest on behalf of the deployment directory.

<table>
	<th>
		<td>env</td>
		<td>location</td>
		<td>remote</td> 
	</th>
	<tr>
		<td>test</td>
		<td>testgus.87c.us</td>
		<td>/home/eight7cu/repos/testgus.git</td> 
	</tr>
	<tr>
		<td>live</td>
		<td>gus1pt0.87c.us</td>
		<td>/home/eight7cu/repos/gus1pt0.git</td> 
	</tr>
</table>