<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


		
		
<xsl:template match="/">
  <html>
  <head>
  
<meta charset="utf-8"/>
<style type="text/css">

  
  @import url(formatting.css);

</style>
<title>Data set for George Street (Sydney, N.S.W.)</title>

</head>
  <body>
  
  <h2>George Street (Sydney, N.S.W.)
  <!--<xsl:for-each select="collection/header">
  <xsl:value-of select="dataType"/>,
  <xsl:value-of select="dataSet"/>,
  <xsl:value-of select="dateRange"/>
  </xsl:for-each>-->
  </h2>
   
  <div id="demo">
  <p>


<!--<xsl:for-each select="SearchTransaction/question/collection/facetedNavigationConfConfig/facetDefinitions/FacetDefinition">
<xsl:value-of select="facetName"/><br />
<xsl:value-of select="data"/><br />
</xsl:for-each>-->


  
  <xsl:for-each select="collection/item">
  
  <xsl:variable name="image">
  <xsl:value-of select="image_url"/>
  </xsl:variable>

  <xsl:variable name="record-link">
  <xsl:value-of select="record_url"/>
  </xsl:variable>


 <div id="tweed">
<div class="details">

<strong>Title: </strong> <xsl:value-of select="description"/><br />
<strong>Date: </strong><xsl:value-of select="date"/><br />
<strong>Topics: </strong><xsl:value-of select="topics"/><br />
<strong>Featured places: </strong><xsl:value-of select="places"/><br />
<strong>Url: </strong><a href="{$record-link}">Open record</a><br />
<strong>Suburb: </strong><xsl:value-of select="suburb"/><br />
<strong>Location: </strong><xsl:value-of select="street"/><br />
</div>
<img src="{$image}"/>

</div>







<!--<a href="{$acms-link_id}"><img src="{$thumbnaillink_id}"/></a>-->

</xsl:for-each>




 
   </p>
  
   </div>
  </body>
  </html>
</xsl:template>

</xsl:stylesheet>

